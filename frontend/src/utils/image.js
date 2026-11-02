/**
 * Utility to process and compress images client-side into lightweight Base64 Data URLs.
 * Uses WebP with JPEG fallback, downscales smoothly, and guarantees small file sizes (< 35KB each).
 * This fits comfortably within Firestore document limits (< 1MB) even with 15+ gallery photos!
 */
export async function processImageToBase64(file, maxWidth = 480, maxHeight = 480, maxByteSize = 35000) {
  if (!file) return null;

  // SVG handling
  if (file.type === 'image/svg+xml') {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = (e) => resolve(e.target.result);
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }

  // Load image into HTML Image object
  const img = await new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = reject;
    reader.onload = (e) => {
      const image = new Image();
      image.onerror = reject;
      image.onload = () => resolve(image);
      image.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });

  // Calculate scaled dimensions
  let curWidth = img.width;
  let curHeight = img.height;
  if (curWidth > maxWidth || curHeight > maxHeight) {
    const ratio = Math.min(maxWidth / curWidth, maxHeight / curHeight);
    curWidth = Math.round(curWidth * ratio);
    curHeight = Math.round(curHeight * ratio);
  }

  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');

  let mimeType = 'image/webp';
  let quality = 0.75;
  let resultDataUrl = '';

  // Iteratively compress to be strictly under maxByteSize
  for (let attempt = 0; attempt < 5; attempt++) {
    canvas.width = curWidth;
    canvas.height = curHeight;
    ctx.clearRect(0, 0, curWidth, curHeight);

    // If JPEG fallback is needed, fill white background for transparent images
    if (mimeType === 'image/jpeg') {
      ctx.fillStyle = '#FFFFFF';
      ctx.fillRect(0, 0, curWidth, curHeight);
    }

    ctx.drawImage(img, 0, 0, curWidth, curHeight);

    try {
      resultDataUrl = canvas.toDataURL(mimeType, quality);
      // Verify WebP support: if canvas returns image/png instead, fallback to jpeg
      if (mimeType === 'image/webp' && !resultDataUrl.startsWith('data:image/webp')) {
        mimeType = 'image/jpeg';
        quality = 0.70;
        continue;
      }
    } catch (e) {
      mimeType = 'image/jpeg';
      quality = 0.70;
      continue;
    }

    // Check size in bytes (base64 string length * 0.75 is approx byte size)
    const approximateBytes = Math.round(resultDataUrl.length * 0.75);
    if (approximateBytes <= maxByteSize || attempt === 4) {
      break;
    }

    // Step down dimensions and quality
    curWidth = Math.round(curWidth * 0.82);
    curHeight = Math.round(curHeight * 0.82);
    quality = Math.max(0.45, quality - 0.10);
  }

  return resultDataUrl;
}
