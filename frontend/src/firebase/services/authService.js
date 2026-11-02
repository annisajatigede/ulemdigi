import {
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  signInWithPopup,
  GoogleAuthProvider,
  signOut,
  sendPasswordResetEmail,
  updateProfile,
  onAuthStateChanged
} from 'firebase/auth';
import { doc, getDoc, setDoc, updateDoc, serverTimestamp } from 'firebase/firestore';
import { auth, db } from '../config';

const googleProvider = new GoogleAuthProvider();

export async function loginWithEmail(email, password) {
  const credential = await signInWithEmailAndPassword(auth, email, password);
  const userDoc = await getUserProfile(credential.user.uid);
  return { user: credential.user, profile: userDoc };
}

export async function registerWithEmail(email, password, name) {
  const credential = await createUserWithEmailAndPassword(auth, email, password);
  await updateProfile(credential.user, { displayName: name });

  // Default role is admin if matches admin email or first user
  const isAdminEmail = email.toLowerCase().includes('admin') || email.toLowerCase().includes('annisajatigede');
  const role = isAdminEmail ? 'admin' : 'user';

  const profileData = {
    uid: credential.user.uid,
    name: name,
    email: email,
    role: role,
    created_at: serverTimestamp(),
    updated_at: serverTimestamp()
  };

  await setDoc(doc(db, 'users', credential.user.uid), profileData);
  return { user: credential.user, profile: profileData };
}

export async function loginWithGoogle() {
  const credential = await signInWithPopup(auth, googleProvider);
  let profile = await getUserProfile(credential.user.uid);

  if (!profile) {
    const isAdminEmail = credential.user.email?.toLowerCase().includes('admin') || credential.user.email?.toLowerCase().includes('annisajatigede');
    profile = {
      uid: credential.user.uid,
      name: credential.user.displayName || 'Pengguna Forkoway',
      email: credential.user.email,
      role: isAdminEmail ? 'admin' : 'user',
      created_at: serverTimestamp(),
      updated_at: serverTimestamp()
    };
    await setDoc(doc(db, 'users', credential.user.uid), profile);
  }

  return { user: credential.user, profile };
}

export async function logoutUser() {
  await signOut(auth);
}

export async function resetPassword(email) {
  await sendPasswordResetEmail(auth, email);
}

export async function getUserProfile(uid) {
  try {
    const docRef = doc(db, 'users', uid);
    const snap = await getDoc(docRef);
    if (snap.exists()) {
      return snap.data();
    }
  } catch (err) {
    console.error('Failed to get user profile:', err);
  }
  return null;
}

export async function updateUserProfile(uid, data) {
  const docRef = doc(db, 'users', uid);
  await updateDoc(docRef, {
    ...data,
    updated_at: serverTimestamp()
  });
}

export function subscribeAuthState(callback) {
  return onAuthStateChanged(auth, async (user) => {
    if (user) {
      let profile = await getUserProfile(user.uid);
      if (!profile) {
        const isAdmin = user.email?.toLowerCase().includes('admin');
        profile = {
          uid: user.uid,
          name: user.displayName || 'Pengguna Forkoway',
          email: user.email,
          role: isAdmin ? 'admin' : 'user'
        };
      }
      callback(user, profile);
    } else {
      callback(null, null);
    }
  });
}
