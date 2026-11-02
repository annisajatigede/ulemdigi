import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { getFirestore } from 'firebase/firestore';
import { getStorage } from 'firebase/storage';

const firebaseConfig = {
  apiKey: "AIzaSyDiEJL4xRQ9GKujWVp7iKMeGO_7idt502w",
  authDomain: "sumedangonline-180419.firebaseapp.com",
  databaseURL: "https://sumedangonline-180419.firebaseio.com",
  projectId: "sumedangonline-180419",
  storageBucket: "sumedangonline-180419.firebasestorage.app",
  messagingSenderId: "283298459271",
  appId: "1:283298459271:web:5b2b802c16b129f92a1222"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);

export { app, auth, db, storage };
export default app;
