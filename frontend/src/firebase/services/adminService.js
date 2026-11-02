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
  orderBy,
  serverTimestamp
} from 'firebase/firestore';
import { db } from '../config';
import { defaultTemplates, defaultPlans, defaultMusics, defaultSettings } from './defaults';

// 1. Settings
export async function getSiteSettings() {
  try {
    const docRef = doc(db, 'settings', 'general');
    const snap = await getDoc(docRef);
    if (snap.exists()) {
      return { ...defaultSettings, ...snap.data() };
    }
  } catch (err) {
    console.warn('Failed to load settings from Firestore, using default:', err);
  }
  return defaultSettings;
}

export async function updateSiteSettings(data) {
  const docRef = doc(db, 'settings', 'general');
  await setDoc(docRef, { ...data, updated_at: serverTimestamp() }, { merge: true });
}

// 2. Templates
export async function getAllTemplates() {
  try {
    const snap = await getDocs(query(collection(db, 'templates'), orderBy('name', 'asc')));
    if (!snap.empty) {
      return snap.docs.map(d => ({ id: d.id, ...d.data() }));
    }
  } catch (err) {
    console.warn('Failed to load templates from Firestore, using default:', err);
  }
  return defaultTemplates;
}

export async function saveTemplate(id, data) {
  if (id) {
    await updateDoc(doc(db, 'templates', id), { ...data, updated_at: serverTimestamp() });
    return id;
  } else {
    const docRef = await addDoc(collection(db, 'templates'), {
      ...data,
      created_at: serverTimestamp(),
      updated_at: serverTimestamp()
    });
    return docRef.id;
  }
}

export async function deleteTemplate(id) {
  await deleteDoc(doc(db, 'templates', id));
}

// 3. Plans
export async function getAllPlans() {
  try {
    const snap = await getDocs(query(collection(db, 'plans'), orderBy('price', 'asc')));
    if (!snap.empty) {
      return snap.docs.map(d => ({ id: d.id, ...d.data() }));
    }
  } catch (err) {
    console.warn('Failed to load plans from Firestore, using default:', err);
  }
  return defaultPlans;
}

export async function savePlan(id, data) {
  if (id) {
    await updateDoc(doc(db, 'plans', id), { ...data, updated_at: serverTimestamp() });
    return id;
  } else {
    const docRef = await addDoc(collection(db, 'plans'), {
      ...data,
      created_at: serverTimestamp(),
      updated_at: serverTimestamp()
    });
    return docRef.id;
  }
}

// 4. Musics
export async function getAllMusics() {
  try {
    const snap = await getDocs(query(collection(db, 'musics'), orderBy('title', 'asc')));
    if (!snap.empty) {
      return snap.docs.map(d => ({ id: d.id, ...d.data() }));
    }
  } catch (err) {
    console.warn('Failed to load musics from Firestore, using default:', err);
  }
  return defaultMusics;
}

export async function saveMusic(id, data) {
  if (id) {
    await updateDoc(doc(db, 'musics', id), { ...data, updated_at: serverTimestamp() });
    return id;
  } else {
    const docRef = await addDoc(collection(db, 'musics'), {
      ...data,
      created_at: serverTimestamp(),
      updated_at: serverTimestamp()
    });
    return docRef.id;
  }
}

export async function deleteMusic(id) {
  await deleteDoc(doc(db, 'musics', id));
}

// 5. Users
export async function getAllUsers() {
  try {
    const snap = await getDocs(collection(db, 'users'));
    return snap.docs.map(d => ({ id: d.id, ...d.data() }));
  } catch (err) {
    console.warn('Failed to load users from Firestore:', err);
    return [];
  }
}

// 6. Transactions
export async function getAllTransactions() {
  try {
    const snap = await getDocs(query(collection(db, 'transactions'), orderBy('created_at', 'desc')));
    return snap.docs.map(d => ({ id: d.id, ...d.data() }));
  } catch (err) {
    console.warn('Failed to load transactions from Firestore:', err);
    return [];
  }
}
