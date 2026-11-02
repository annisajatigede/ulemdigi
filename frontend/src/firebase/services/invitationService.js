import {
  collection,
  doc,
  getDoc,
  getDocs,
  setDoc,
  addDoc,
  updateDoc,
  deleteDoc,
  query,
  where,
  orderBy,
  serverTimestamp,
  onSnapshot
} from 'firebase/firestore';
import { db } from '../config';
import { demoInvitation, defaultTemplates, getSafeData } from './defaults';

/**
 * Get invitation by custom slug
 */
export async function getInvitationBySlug(slug) {
  if (slug === 'alex-olivia') {
    try {
      const q = query(collection(db, 'invitations'), where('slug', '==', slug));
      const snap = await getDocs(q);
      if (!snap.empty) {
        const docSnap = snap.docs[0];
        const inv = { id: docSnap.id, ...docSnap.data() };
        const eventsSnap = await getDocs(
          query(collection(db, 'invitations', docSnap.id, 'events'), orderBy('start_at', 'asc'))
        );
        inv.invitation_events = eventsSnap.docs.map(d => ({ id: d.id, ...d.data() }));
        if (inv.template_id) {
          const tmpl = defaultTemplates.find(t => t.id === inv.template_id.toString());
          if (tmpl) inv.templates = tmpl;
        }
        return inv;
      }
    } catch (e) {
      console.warn('Falling back to demo invitation for alex-olivia:', e);
    }
    return demoInvitation;
  }

  try {
    const q = query(collection(db, 'invitations'), where('slug', '==', slug));
    const snap = await getDocs(q);
    if (snap.empty) return null;

    const docSnap = snap.docs[0];
    const invitation = { id: docSnap.id, ...docSnap.data() };

    // Fetch events subcollection
    const eventsSnap = await getDocs(
      query(collection(db, 'invitations', docSnap.id, 'events'), orderBy('start_at', 'asc'))
    );
    invitation.invitation_events = eventsSnap.docs.map(d => ({ id: d.id, ...d.data() }));

    // Fetch template data if template_id is present
    if (invitation.template_id) {
      const tmplSnap = await getDoc(doc(db, 'templates', invitation.template_id.toString()));
      if (tmplSnap.exists()) {
        invitation.templates = { id: tmplSnap.id, ...tmplSnap.data() };
      } else {
        const tmpl = defaultTemplates.find(t => t.id === invitation.template_id.toString());
        if (tmpl) invitation.templates = tmpl;
      }
    }

    return invitation;
  } catch (err) {
    console.error('Error fetching invitation by slug:', err);
    return null;
  }
}

/**
 * Get all invitations belonging to a user
 */
export async function getUserInvitations(userId) {
  const q = query(
    collection(db, 'invitations'),
    where('user_id', '==', userId)
  );
  const snap = await getDocs(q);
  const invitations = [];

  for (const d of snap.docs) {
    const inv = { id: d.id, ...d.data() };
    if (inv.template_id) {
      const tmplSnap = await getDoc(doc(db, 'templates', inv.template_id.toString()));
      if (tmplSnap.exists()) {
        inv.templates = { id: tmplSnap.id, ...tmplSnap.data() };
      }
    }
    invitations.push(inv);
  }

  return invitations;
}

/**
 * Get single invitation by ID with events
 */
export async function getInvitationById(id) {
  const docRef = doc(db, 'invitations', id);
  const snap = await getDoc(docRef);
  if (!snap.exists()) return null;

  const invitation = { id: snap.id, ...snap.data() };

  const eventsSnap = await getDocs(
    query(collection(db, 'invitations', id, 'events'), orderBy('start_at', 'asc'))
  );
  invitation.invitation_events = eventsSnap.docs.map(d => ({ id: d.id, ...d.data() }));

  if (invitation.template_id) {
    const tmplSnap = await getDoc(doc(db, 'templates', invitation.template_id.toString()));
    if (tmplSnap.exists()) {
      invitation.templates = { id: tmplSnap.id, ...tmplSnap.data() };
    }
  }

  return invitation;
}

/**
 * Create or update an invitation
 */
export async function saveInvitation(id, data, events = []) {
  if (id) {
    const docRef = doc(db, 'invitations', id);
    await updateDoc(docRef, {
      ...data,
      updated_at: serverTimestamp()
    });

    if (events && events.length > 0) {
      for (const ev of events) {
        if (ev.id) {
          await updateDoc(doc(db, 'invitations', id, 'events', ev.id), {
            ...ev,
            updated_at: serverTimestamp()
          });
        } else {
          await addDoc(collection(db, 'invitations', id, 'events'), {
            ...ev,
            created_at: serverTimestamp()
          });
        }
      }
    }
    return id;
  } else {
    const docRef = await addDoc(collection(db, 'invitations'), {
      ...data,
      created_at: serverTimestamp(),
      updated_at: serverTimestamp()
    });

    if (events && events.length > 0) {
      for (const ev of events) {
        await addDoc(collection(db, 'invitations', docRef.id, 'events'), {
          ...ev,
          created_at: serverTimestamp()
        });
      }
    }
    return docRef.id;
  }
}

/**
 * Toggle Publish status
 */
export async function togglePublishInvitation(id, currentStatus) {
  const newStatus = currentStatus === 'published' ? 'draft' : 'published';
  const docRef = doc(db, 'invitations', id);
  await updateDoc(docRef, {
    status: newStatus,
    published_at: newStatus === 'published' ? serverTimestamp() : null,
    updated_at: serverTimestamp()
  });
  return newStatus;
}

/**
 * Realtime listener for wishes / doa restu
 */
export function subscribeWishes(invitationId, callback) {
  const q = query(
    collection(db, 'invitations', invitationId, 'wishes'),
    orderBy('created_at', 'desc')
  );
  return onSnapshot(q, (snap) => {
    const wishes = snap.docs.map(d => ({
      id: d.id,
      ...d.data(),
      created_at: d.data().created_at?.toDate ? d.data().created_at.toDate().toISOString() : new Date().toISOString()
    }));
    callback(wishes);
  });
}

/**
 * Submit RSVP & Wish
 */
export async function submitRsvpAndWish(invitationId, rsvpData) {
  // 1. Add to wishes subcollection
  await addDoc(collection(db, 'invitations', invitationId, 'wishes'), {
    name: rsvpData.name,
    message: rsvpData.message,
    rsvp_status: rsvpData.rsvp_status,
    guest_count: rsvpData.guest_count || 1,
    created_at: serverTimestamp()
  });

  // 2. If guest personal_token is present, update guest record
  if (rsvpData.personal_token) {
    const q = query(
      collection(db, 'invitations', invitationId, 'guests'),
      where('personal_token', '==', rsvpData.personal_token)
    );
    const snap = await getDocs(q);
    if (!snap.empty) {
      await updateDoc(doc(db, 'invitations', invitationId, 'guests', snap.docs[0].id), {
        rsvp_status: rsvpData.rsvp_status,
        guest_count: rsvpData.guest_count || 1,
        message: rsvpData.message,
        updated_at: serverTimestamp()
      });
    }
  }
}

/**
 * Guest management subcollection
 */
export async function getInvitationGuests(invitationId) {
  const snap = await getDocs(collection(db, 'invitations', invitationId, 'guests'));
  return snap.docs.map(d => ({ id: d.id, ...d.data() }));
}

export async function addGuest(invitationId, guestData) {
  const token = Math.random().toString(36).substring(2, 8);
  const docRef = await addDoc(collection(db, 'invitations', invitationId, 'guests'), {
    ...guestData,
    personal_token: token,
    created_at: serverTimestamp()
  });
  return { id: docRef.id, ...guestData, personal_token: token };
}

export async function deleteGuest(invitationId, guestId) {
  await deleteDoc(doc(db, 'invitations', invitationId, 'guests', guestId));
}

export async function checkInGuest(invitationId, guestId) {
  const docRef = doc(db, 'invitations', invitationId, 'guests', guestId);
  await updateDoc(docRef, {
    is_attended: true,
    check_in_at: serverTimestamp()
  });
}
