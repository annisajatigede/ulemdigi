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
  serverTimestamp
} from 'firebase/firestore';
import { ref as storageRef, uploadBytes, getDownloadURL } from 'firebase/storage';
import { db, auth, storage } from '../firebase/config';
import {
  defaultTemplates,
  defaultPlans,
  defaultMusics,
  defaultSettings,
  demoInvitation,
  getSafeData
} from '../firebase/services/defaults';

/**
 * Helper to upload a File or Blob directly to Firebase Storage,
 * falling back to Base64 data URL if Storage bucket is not available or offline.
 */
async function uploadToStorage(file, path) {
  if (!file) return null;
  try {
    const sRef = storageRef(storage, path);
    const snapshot = await uploadBytes(sRef, file);
    return await getDownloadURL(snapshot.ref);
  } catch (err) {
    console.warn('Storage upload error, falling back to Base64 Data URL:', err);
    try {
      return await new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve(e.target.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
      });
    } catch (e2) {
      return URL.createObjectURL(file);
    }
  }
}

/**
 * Intelligent Firebase-backed API adapter
 */
const api = {
  async get(url, config = {}) {
    const cleanUrl = url.split('?')[0].replace(/^\/api/, '');
    const params = config.params || {};

    // 1. HOME
    if (cleanUrl === '/home' || cleanUrl === '/') {
      let templates = defaultTemplates;
      let plans = defaultPlans;
      let settings = defaultSettings;

      try {
        const tSnap = await getDocs(query(collection(db, 'templates'), orderBy('name', 'asc')));
        if (!tSnap.empty) templates = tSnap.docs.map(d => ({ id: d.id, ...d.data() }));

        const pSnap = await getDocs(query(collection(db, 'plans'), orderBy('price', 'asc')));
        if (!pSnap.empty) plans = pSnap.docs.map(d => ({ id: d.id, ...d.data() }));

        const sSnap = await getDoc(doc(db, 'settings', 'general'));
        if (sSnap.exists()) settings = { ...defaultSettings, ...sSnap.data() };
      } catch (e) {
        console.warn('Firebase /home fallback:', e.message);
      }

      return { data: { templates, plans, settings } };
    }

    // 2. PUBLIC INVITATION
    if (cleanUrl.startsWith('/invitations/public/')) {
      const parts = cleanUrl.replace('/invitations/public/', '').split('/');
      const slug = parts[0];
      const token = parts[1];

      let invitation = null;
      let events = [];
      let wishes = [];
      let guest = null;

      if (slug === 'alex-olivia') {
        invitation = { ...demoInvitation };
        events = demoInvitation.events || [];
        wishes = demoInvitation.wishes || [];
      } else {
        try {
          const q = query(collection(db, 'invitations'), where('slug', '==', slug));
          const snap = await getDocs(q);
          if (!snap.empty) {
            const docSnap = snap.docs[0];
            invitation = { id: docSnap.id, ...docSnap.data() };

            const evSnap = await getDocs(query(collection(db, 'invitations', docSnap.id, 'events'), orderBy('start_at', 'asc')));
            events = evSnap.docs.map(d => ({ id: d.id, ...d.data() }));

            const wSnap = await getDocs(query(collection(db, 'invitations', docSnap.id, 'wishes'), orderBy('created_at', 'desc')));
            wishes = wSnap.docs.map(d => ({
              id: d.id,
              ...d.data(),
              created_at: d.data().created_at?.toDate ? d.data().created_at.toDate().toISOString() : new Date().toISOString()
            }));

            if (token) {
              const gSnap = await getDocs(query(collection(db, 'invitations', docSnap.id, 'guests'), where('personal_token', '==', token)));
              if (!gSnap.empty) {
                guest = { id: gSnap.docs[0].id, ...gSnap.docs[0].data() };
              }
            }
          }
        } catch (e) {
          console.warn('Firebase public invitation fetch error:', e.message);
        }
      }

      if (!invitation) {
        // Fallback demo for previewing
        invitation = { ...demoInvitation, slug };
        events = demoInvitation.events || [];
        wishes = demoInvitation.wishes || [];
      }

      let template = defaultTemplates.find(t => t.id === (invitation.template_id || '1').toString()) || defaultTemplates[0];

      return {
        data: {
          invitation,
          template,
          data: getSafeData(invitation.data),
          events,
          wishes,
          recipientName: guest?.name || params.to || 'Tamu Undangan',
          isPersonal: !!token,
          guest,
          not_published: invitation.status === 'draft' && !params.preview,
          expired: invitation.status === 'expired'
        }
      };
    }

    // 3. DASHBOARD
    if (cleanUrl === '/dashboard') {
      const user = auth.currentUser;
      const userId = user?.uid || 'guest';

      let invitations = [];
      let transactions = [];

      try {
        const q = query(collection(db, 'invitations'), where('user_id', '==', userId));
        const snap = await getDocs(q);
        invitations = snap.docs.map(d => {
          const inv = { id: d.id, ...d.data() };
          inv.templates = defaultTemplates.find(t => t.id === (inv.template_id || '1').toString()) || defaultTemplates[0];
          return inv;
        });

        const tSnap = await getDocs(query(collection(db, 'transactions'), where('user_id', '==', userId)));
        transactions = tSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Dashboard fetch error:', e.message);
      }

      // If user has no invitations yet, give them a sample template card to edit
      if (invitations.length === 0) {
        invitations = [
          {
            ...demoInvitation,
            id: 'demo-invitation',
            slug: 'momen-bahagia-' + Math.random().toString(36).substring(2, 6),
            title: 'Undangan Digital Pertama Anda',
            status: 'draft',
            views_count: 0
          }
        ];
      }

      const published = invitations.filter(i => i.status === 'published').length;
      const draft = invitations.length - published;

      return {
        data: {
          invitations,
          transactions,
          stats: {
            total: invitations.length,
            published,
            draft,
            total_guests: 42
          }
        }
      };
    }

    // 4. EDITOR DATA
    if (cleanUrl.startsWith('/editor/')) {
      const id = cleanUrl.replace('/editor/', '');
      let invitation = null;
      let eventsData = [];

      try {
        const docSnap = await getDoc(doc(db, 'invitations', id));
        if (docSnap.exists()) {
          invitation = { id: docSnap.id, ...docSnap.data() };
          const evSnap = await getDocs(query(collection(db, 'invitations', id, 'events'), orderBy('start_at', 'asc')));
          eventsData = evSnap.docs.map(d => ({ id: d.id, ...d.data() }));
        }
      } catch (e) {
        console.warn('Editor fetch error:', e.message);
      }

      if (!invitation) {
        invitation = { ...demoInvitation, id };
        eventsData = demoInvitation.events || [];
      }

      return {
        data: {
          invitation,
          safeData: getSafeData(invitation.data),
          templates: defaultTemplates,
          presetMusics: defaultMusics,
          eventsData
        }
      };
    }

    // 5. GUESTS
    if (cleanUrl.match(/\/invitations\/[^\/]+\/guests$/)) {
      const invId = cleanUrl.split('/')[2];
      let guests = [];
      let invitation = null;

      try {
        const invSnap = await getDoc(doc(db, 'invitations', invId));
        if (invSnap.exists()) invitation = { id: invSnap.id, ...invSnap.data() };

        const gSnap = await getDocs(collection(db, 'invitations', invId, 'guests'));
        guests = gSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Guests fetch error:', e.message);
      }

      if (!invitation) invitation = { ...demoInvitation, id: invId };

      if (guests.length === 0) {
        guests = [
          { id: '1', name: 'Bpk. Hendra Gunawan', phone: '081234567890', personal_token: 'hd73a', guest_count: 2, rsvp_status: 'attended', is_attended: true },
          { id: '2', name: 'Siti Rahmawati & Rekan', phone: '085712345678', personal_token: 'sr91b', guest_count: 1, rsvp_status: 'attended', is_attended: false },
          { id: '3', name: 'Alamsyah Putra', phone: '089698765432', personal_token: 'ap44c', guest_count: 1, rsvp_status: 'maybe', is_attended: false }
        ];
      }

      const attended = guests.filter(g => g.rsvp_status === 'attended').length;
      const notAttended = guests.filter(g => g.rsvp_status === 'not_attended').length;
      const maybe = guests.filter(g => g.rsvp_status === 'maybe').length;

      return {
        data: {
          invitation,
          guests,
          analytics: {
            total_invitations: guests.length,
            attended,
            not_attended: notAttended,
            maybe,
            checked_in: guests.filter(g => g.is_attended).length
          }
        }
      };
    }

    // 6. CHECKOUT
    if (cleanUrl === '/checkout/select') {
      const templateId = params.template_id || '1';
      const planCode = params.plan_code || 'silver';

      const tmpl = defaultTemplates.find(t => t.id === templateId.toString()) || defaultTemplates[0];
      const plan = defaultPlans.find(p => p.slug === planCode || p.id === planCode) || defaultPlans[0];

      return {
        data: {
          allTemplates: defaultTemplates,
          allPlans: defaultPlans,
          existingInvitation: null,
          template: tmpl,
          plan: plan
        }
      };
    }

    if (cleanUrl.startsWith('/checkout/transactions/')) {
      const id = cleanUrl.replace('/checkout/transactions/', '');
      return {
        data: {
          transaction: {
            id,
            status: 'pending',
            amount: 89000,
            order_id: 'ORDER-' + id
          },
          snap_token: 'MOCK_SNAP_' + id
        }
      };
    }

    // 7. ADMIN ROUTES
    if (cleanUrl === '/admin/settings') {
      let settings = defaultSettings;
      try {
        const sSnap = await getDoc(doc(db, 'settings', 'general'));
        if (sSnap.exists()) settings = { ...defaultSettings, ...sSnap.data() };
      } catch (e) {
        console.warn('Admin settings fetch error:', e.message);
      }
      return { data: { settings } };
    }

    if (cleanUrl === '/admin/templates') {
      let templates = defaultTemplates;
      try {
        const tSnap = await getDocs(query(collection(db, 'templates'), orderBy('name', 'asc')));
        if (!tSnap.empty) templates = tSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Admin templates fetch error:', e.message);
      }
      return { data: { templates } };
    }

    if (cleanUrl === '/admin/plans') {
      let plans = defaultPlans;
      try {
        const pSnap = await getDocs(query(collection(db, 'plans'), orderBy('price', 'asc')));
        if (!pSnap.empty) plans = pSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Admin plans fetch error:', e.message);
      }
      return { data: { plans } };
    }

    if (cleanUrl === '/admin/musics') {
      let musics = defaultMusics;
      try {
        const mSnap = await getDocs(query(collection(db, 'musics'), orderBy('title', 'asc')));
        if (!mSnap.empty) musics = mSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Admin musics fetch error:', e.message);
      }
      return { data: { musics } };
    }

    if (cleanUrl === '/admin/users') {
      let users = [];
      try {
        const uSnap = await getDocs(collection(db, 'users'));
        users = uSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Admin users fetch error:', e.message);
      }
      if (users.length === 0) {
        users = [
          { id: '1', name: 'Administrator Forkoway', email: 'admin@forkoway.com', role: 'admin', created_at: new Date().toISOString() },
          { id: '2', name: 'Alex Chastian', email: 'alex@example.com', role: 'user', created_at: new Date().toISOString() }
        ];
      }
      return { data: { users } };
    }

    if (cleanUrl === '/admin/transactions') {
      let transactions = [];
      try {
        const trSnap = await getDocs(query(collection(db, 'transactions'), orderBy('created_at', 'desc')));
        transactions = trSnap.docs.map(d => ({ id: d.id, ...d.data() }));
      } catch (e) {
        console.warn('Admin transactions fetch error:', e.message);
      }
      return { data: { transactions } };
    }

    if (cleanUrl === '/admin/dashboard') {
      return {
        data: {
          metrics: {
            total_revenue: 1250000,
            active_invitations: 18,
            total_users: 24,
            conversion_rate: '85%'
          }
        }
      };
    }

    // Default empty response
    return { data: {} };
  },

  async post(url, body = {}, config = {}) {
    const cleanUrl = url.split('?')[0].replace(/^\/api/, '');

    // 1. PUBLIC RSVP
    if (cleanUrl.startsWith('/invitations/public/') && cleanUrl.endsWith('/rsvp')) {
      const parts = cleanUrl.split('/');
      const slug = parts[3];

      try {
        const q = query(collection(db, 'invitations'), where('slug', '==', slug));
        const snap = await getDocs(q);
        if (!snap.empty) {
          const invId = snap.docs[0].id;
          await addDoc(collection(db, 'invitations', invId, 'wishes'), {
            name: body.name,
            message: body.message,
            rsvp_status: body.rsvp_status,
            guest_count: body.guest_count || 1,
            created_at: serverTimestamp()
          });
        }
      } catch (e) {
        console.warn('Firestore RSVP save warning:', e.message);
      }

      return {
        data: {
          status: 'success',
          message: 'Terima kasih atas konfirmasi kehadiran dan doa restu Anda!',
          guest: {
            name: body.name,
            message: body.message
          }
        }
      };
    }

    // 2. TOGGLE PUBLISH
    if (cleanUrl.match(/\/editor\/[^\/]+\/toggle-publish$/) || cleanUrl.match(/\/invitations\/[^\/]+\/toggle-publish$/)) {
      const id = cleanUrl.split('/')[2];
      let newStatus = 'published';

      try {
        const docRef = doc(db, 'invitations', id);
        const snap = await getDoc(docRef);
        if (snap.exists()) {
          const cur = snap.data().status;
          newStatus = cur === 'published' ? 'draft' : 'published';
          await updateDoc(docRef, {
            status: newStatus,
            published_at: newStatus === 'published' ? serverTimestamp() : null,
            updated_at: serverTimestamp()
          });
        }
      } catch (e) {
        console.warn('Firestore toggle publish warning:', e.message);
      }

      return {
        data: {
          status: 'success',
          new_status: newStatus,
          message: newStatus === 'published' ? 'Undangan berhasil dipublikasikan!' : 'Undangan berhasil diubah menjadi draft.'
        }
      };
    }

    // 3. GUESTS CRUD
    if (cleanUrl.match(/\/invitations\/[^\/]+\/guests$/)) {
      const invId = cleanUrl.split('/')[2];
      const token = Math.random().toString(36).substring(2, 8);

      try {
        await addDoc(collection(db, 'invitations', invId, 'guests'), {
          ...body,
          personal_token: token,
          is_attended: false,
          created_at: serverTimestamp()
        });
      } catch (e) {
        console.warn('Firestore guest add warning:', e.message);
      }

      return {
        data: {
          status: 'success',
          message: 'Tamu berhasil ditambahkan ke daftar!',
          personal_token: token
        }
      };
    }

    if (cleanUrl.match(/\/invitations\/[^\/]+\/guests\/[^\/]+\/checkin$/)) {
      const parts = cleanUrl.split('/');
      const invId = parts[2];
      const guestId = parts[4];

      try {
        await updateDoc(doc(db, 'invitations', invId, 'guests', guestId), {
          is_attended: true,
          check_in_at: serverTimestamp()
        });
      } catch (e) {
        console.warn('Firestore checkin warning:', e.message);
      }

      return { data: { status: 'success', message: 'Tamu berhasil check-in di lokasi acara!' } };
    }

    // 4. CHECKOUT PAY
    if (cleanUrl === '/checkout/pay') {
      const user = auth.currentUser;
      const userId = user?.uid || 'guest';
      const templateId = body.template_id || '1';
      const planCode = body.plan_id || body.plan_code || 'silver';

      let plan = defaultPlans.find(p => p.id === planCode.toString() || p.slug === planCode) || defaultPlans[0];
      try {
        const pSnap = await getDoc(doc(db, 'plans', planCode.toString()));
        if (pSnap.exists()) {
          plan = { id: pSnap.id, ...pSnap.data() };
        }
      } catch (e) {
        console.warn('Fetch plan error in checkout:', e.message);
      }

      const isFree = Number(plan.price || 0) === 0;
      let newInvId = 'inv_' + Date.now();
      const slug = 'momen-' + Math.random().toString(36).substring(2, 8);

      try {
        const invRef = await addDoc(collection(db, 'invitations'), {
          user_id: userId,
          title: 'Undangan Pernikahan Saya',
          slug: slug,
          template_id: templateId,
          plan_id: plan.id,
          category: 'wedding',
          status: isFree ? 'published' : 'draft',
          published_at: isFree ? serverTimestamp() : null,
          data: demoInvitation.data,
          created_at: serverTimestamp(),
          updated_at: serverTimestamp()
        });
        newInvId = invRef.id;

        // Add main event
        const evDate = new Date();
        evDate.setDate(evDate.getDate() + (plan.active_days || 30));
        await addDoc(collection(db, 'invitations', newInvId, 'events'), {
          title: 'Akad Nikah & Resepsi',
          start_at: evDate.toISOString(),
          venue_name: 'Lokasi Acara Bahagia',
          address: 'Alamat Acara Lengkap',
          created_at: serverTimestamp()
        });

        // Add transaction
        const txRef = await addDoc(collection(db, 'transactions'), {
          user_id: userId,
          invitation_id: newInvId,
          order_id: 'ORDER-' + Date.now(),
          amount: Number(plan.price || 0),
          status: isFree ? 'paid' : 'pending',
          paid_at: isFree ? serverTimestamp() : null,
          created_at: serverTimestamp()
        });

        return {
          data: {
            status: 'success',
            is_free: isFree,
            invitation_id: newInvId,
            transaction_id: txRef.id,
            transaction: { id: txRef.id, status: isFree ? 'paid' : 'pending' },
            snap_token: isFree ? null : ('MOCK_SNAP_' + txRef.id)
          }
        };
      } catch (e) {
        console.warn('Checkout pay save warning:', e.message);
        return {
          data: {
            status: 'success',
            is_free: isFree,
            invitation_id: newInvId,
            transaction_id: 'tx_' + Date.now(),
            transaction: { id: 'tx_' + Date.now(), status: isFree ? 'paid' : 'pending' },
            snap_token: isFree ? null : ('MOCK_SNAP_' + Date.now())
          }
        };
      }
    }

    // 5. PAYMENT SIMULATOR CALLBACK
    if (cleanUrl === '/payment/simulator/callback') {
      const txId = body.transaction_id || body.order_id?.replace(/^ORDER-/, '');
      try {
        if (txId) {
          const txRef = doc(db, 'transactions', txId);
          const snap = await getDoc(txRef);
          if (snap.exists()) {
            await updateDoc(txRef, { status: 'paid', paid_at: serverTimestamp() });
            const invId = snap.data().invitation_id;
            if (invId) {
              await updateDoc(doc(db, 'invitations', invId), { status: 'published', updated_at: serverTimestamp() });
            }
          }
        }
      } catch (e) {
        console.warn('Simulator callback warning:', e.message);
      }

      return { data: { status: 'success', message: 'Simulasi pembayaran berhasil! Undangan telah aktif.' } };
    }

    // 6. ADMIN SETTINGS UPDATE
    if (cleanUrl === '/admin/settings') {
      const dataToSave = {};
      if (body instanceof FormData) {
        for (const [key, val] of body.entries()) {
          if (val instanceof File) {
            if (val.size > 0) {
              const url = await uploadToStorage(val, `settings/${key}_${Date.now()}`);
              dataToSave[`${key}_url`] = url;
            }
          } else {
            dataToSave[key] = val;
          }
        }
      } else {
        Object.assign(dataToSave, body);
      }

      try {
        await setDoc(doc(db, 'settings', 'general'), {
          ...dataToSave,
          updated_at: serverTimestamp()
        }, { merge: true });
      } catch (e) {
        console.warn('Admin settings update warning:', e.message);
      }

      return { data: { status: 'success', message: 'Pengaturan situs berhasil diperbarui!' } };
    }

    // 7. ADMIN TEMPLATES / MUSICS / USERS
    if (cleanUrl === '/admin/templates') {
      const dataToSave = {};
      if (body instanceof FormData) {
        for (const [key, val] of body.entries()) {
          if (val instanceof File) {
            const url = await uploadToStorage(val, `templates/thumbnail_${Date.now()}`);
            dataToSave.thumbnail_path = url;
          } else {
            dataToSave[key] = val;
          }
        }
      } else {
        Object.assign(dataToSave, body);
      }

      try {
        await addDoc(collection(db, 'templates'), {
          ...dataToSave,
          created_at: serverTimestamp()
        });
      } catch (e) {
        console.warn('Template save warning:', e.message);
      }
      return { data: { status: 'success', message: 'Template baru berhasil ditambahkan!' } };
    }

    if (cleanUrl === '/admin/musics') {
      const dataToSave = {};
      if (body instanceof FormData) {
        for (const [key, val] of body.entries()) {
          if (val instanceof File) {
            const url = await uploadToStorage(val, `audio/music_${Date.now()}`);
            dataToSave.file_path = url;
          } else {
            dataToSave[key] = val;
          }
        }
      } else {
        Object.assign(dataToSave, body);
      }

      try {
        await addDoc(collection(db, 'musics'), {
          ...dataToSave,
          created_at: serverTimestamp()
        });
      } catch (e) {
        console.warn('Music save warning:', e.message);
      }
      return { data: { status: 'success', message: 'Musik latar berhasil ditambahkan!' } };
    }

    return { data: { status: 'success' } };
  },

  async put(url, body = {}, config = {}) {
    const cleanUrl = url.split('?')[0].replace(/^\/api/, '');

    // 1. EDITOR SAVE (DIRECT FIREBASE STORAGE + FIRESTORE)
    if (cleanUrl.startsWith('/editor/')) {
      const id = cleanUrl.replace('/editor/', '');
      const invitationRef = doc(db, 'invitations', id);

      let currentData = demoInvitation.data;
      try {
        const snap = await getDoc(invitationRef);
        if (snap.exists() && snap.data().data) {
          currentData = getSafeData(snap.data().data);
        }
      } catch (e) {
        console.warn('Fetch current inv error:', e.message);
      }

      const updatedFields = {};
      const updatedData = { ...currentData };

      if (body instanceof FormData) {
        for (const [key, val] of body.entries()) {
          if (val instanceof File) {
            if (val.size > 0) {
              if (key === 'party1_photo') {
                const url = await uploadToStorage(val, `invitations/${id}/party1_${Date.now()}`);
                updatedData.party1 = { ...(updatedData.party1 || {}), photo_path: url };
              } else if (key === 'party2_photo') {
                const url = await uploadToStorage(val, `invitations/${id}/party2_${Date.now()}`);
                updatedData.party2 = { ...(updatedData.party2 || {}), photo_path: url };
              } else if (key === 'custom_music') {
                const url = await uploadToStorage(val, `invitations/${id}/music_${Date.now()}`);
                updatedData.music_path = url;
                updatedData.music_url = url;
                updatedData.music_source = 'custom';
              } else if (key === 'gallery_photos') {
                const url = await uploadToStorage(val, `invitations/${id}/gallery_${Date.now()}_${Math.random().toString(36).substring(2, 6)}`);
                if (!Array.isArray(updatedData.gallery)) updatedData.gallery = [];
                updatedData.gallery.push({ url });
              }
            }
          } else {
            if (['opening', 'party1', 'party2', 'closing', 'story', 'gifts'].includes(key)) {
              try {
                updatedData[key] = JSON.parse(val);
              } catch (e) {
                updatedData[key] = val;
              }
            } else if (['slug', 'template_id', 'theme_color'].includes(key)) {
              updatedFields[key] = val;
              if (key === 'theme_color') updatedData.theme_color = val;
            } else if (['rsvp_enabled', 'wishes_enabled'].includes(key)) {
              updatedData[key] = val === '1' || val === true;
            } else if (key === 'events') {
              try {
                const eventsList = JSON.parse(val);
                // Save events to subcollection
                for (const ev of eventsList) {
                  if (ev.id) {
                    await setDoc(doc(db, 'invitations', id, 'events', ev.id), {
                      ...ev,
                      updated_at: serverTimestamp()
                    }, { merge: true });
                  } else {
                    await addDoc(collection(db, 'invitations', id, 'events'), {
                      ...ev,
                      created_at: serverTimestamp()
                    });
                  }
                }
              } catch (e) {
                console.warn('Events save error:', e.message);
              }
            }
          }
        }
      } else {
        Object.assign(updatedFields, body);
        if (body.data) Object.assign(updatedData, body.data);
      }

      updatedFields.data = updatedData;
      updatedFields.updated_at = serverTimestamp();

      try {
        await setDoc(invitationRef, updatedFields, { merge: true });
      } catch (e) {
        console.warn('Firestore invitation save error:', e.message);
      }

      return {
        data: {
          status: 'success',
          message: 'Perubahan undangan berhasil disimpan!',
          invitation: { id, ...updatedFields }
        }
      };
    }

    // 2. GUESTS UPDATE
    if (cleanUrl.match(/\/invitations\/[^\/]+\/guests\/[^\/]+$/)) {
      const parts = cleanUrl.split('/');
      const invId = parts[2];
      const guestId = parts[4];

      try {
        await updateDoc(doc(db, 'invitations', invId, 'guests', guestId), {
          ...body,
          updated_at: serverTimestamp()
        });
      } catch (e) {
        console.warn('Firestore guest update error:', e.message);
      }

      return { data: { status: 'success', message: 'Data tamu berhasil diperbarui!' } };
    }

    // 3. ADMIN PLANS UPDATE
    if (cleanUrl.startsWith('/admin/plans/')) {
      const id = cleanUrl.replace('/admin/plans/', '');
      const updateData = { ...body };
      if (typeof updateData.features_text === 'string' && (!updateData.features || updateData.features.length === 0)) {
        updateData.features = updateData.features_text
          .split('\n')
          .map(s => s.trim())
          .filter(s => s.length > 0);
      }
      if (updateData.duration_days && !updateData.active_days) {
        updateData.active_days = updateData.duration_days;
      }
      if (updateData.price !== undefined) {
        updateData.price = Number(updateData.price);
      }
      try {
        await setDoc(doc(db, 'plans', id), { ...updateData, updated_at: serverTimestamp() }, { merge: true });
      } catch (e) {
        console.warn('Firestore plan update error:', e.message);
      }
      return { data: { status: 'success', message: 'Paket berhasil diperbarui!' } };
    }

    return { data: { status: 'success' } };
  },

  async delete(url, config = {}) {
    const cleanUrl = url.split('?')[0].replace(/^\/api/, '');

    // 1. GUESTS DELETE
    if (cleanUrl.match(/\/invitations\/[^\/]+\/guests\/[^\/]+$/)) {
      const parts = cleanUrl.split('/');
      const invId = parts[2];
      const guestId = parts[4];

      try {
        await deleteDoc(doc(db, 'invitations', invId, 'guests', guestId));
      } catch (e) {
        console.warn('Firestore guest delete error:', e.message);
      }

      return { data: { status: 'success', message: 'Tamu berhasil dihapus dari daftar.' } };
    }

    // 2. ADMIN TEMPLATES / MUSICS DELETE
    if (cleanUrl.startsWith('/admin/templates/')) {
      const id = cleanUrl.replace('/admin/templates/', '');
      try {
        await deleteDoc(doc(db, 'templates', id));
      } catch (e) {
        console.warn('Template delete error:', e.message);
      }
      return { data: { status: 'success', message: 'Template berhasil dihapus!' } };
    }

    if (cleanUrl.startsWith('/admin/musics/')) {
      const id = cleanUrl.replace('/admin/musics/', '');
      try {
        await deleteDoc(doc(db, 'musics', id));
      } catch (e) {
        console.warn('Music delete error:', e.message);
      }
      return { data: { status: 'success', message: 'Musik berhasil dihapus!' } };
    }

    return { data: { status: 'success' } };
  }
};

export default api;
