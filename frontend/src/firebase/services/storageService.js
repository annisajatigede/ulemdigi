import { ref, uploadBytes, getDownloadURL, deleteObject } from 'firebase/storage';
import { storage } from '../config';

/**
 * Upload a file to Firebase Storage and get its download URL
 * @param {File|Blob} file 
 * @param {string} storagePath e.g. 'invitations/123/photos/bride.jpg'
 * @returns {Promise<string>} Download URL
 */
export async function uploadFile(file, storagePath) {
  if (!file) return null;
  const storageRef = ref(storage, storagePath);
  const snapshot = await uploadBytes(storageRef, file);
  const downloadUrl = await getDownloadURL(snapshot.ref);
  return downloadUrl;
}

/**
 * Delete a file by path
 * @param {string} storagePath 
 */
export async function deleteFile(storagePath) {
  try {
    const storageRef = ref(storage, storagePath);
    await deleteObject(storageRef);
  } catch (err) {
    console.warn('File not found or already deleted:', err.message);
  }
}
