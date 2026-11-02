import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { getFirestore } from 'firebase/firestore';
import { getStorage } from 'firebase/storage';

const firebaseConfig = {
  apiKey: "AIzaSyCTQDutO7mreAtAO98yeXVVTplYmpQRw08",
  authDomain: "forkoway.firebaseapp.com",
  projectId: "forkoway",
  storageBucket: "forkoway.firebasestorage.app",
  messagingSenderId: "311357809397",
  appId: "1:311357809397:web:2df5b2bc6aabc9b985dbbc",
  measurementId: "G-2V3V3YM45T"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);

export { app, auth, db, storage };
export default app;
