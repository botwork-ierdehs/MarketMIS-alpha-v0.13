<template>
  <div class="space-y-4">
    <!-- Signature upload -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Upload Signature (PNG or JPG)
      </label>
      <input
        type="file"
        accept="image/png, image/jpeg"
        @change="handleSignatureUpload"
        class="block w-full text-sm text-gray-600 border rounded p-2"
      />

      <!-- Preview -->
      <div v-if="signature" class="mt-3">
        <p class="text-xs text-gray-500 mb-1">Preview:</p>
        <img
          :src="signaturePreview"
          alt="Signature preview"
          class="border rounded max-h-32"
        />
      </div>
    </div>

    <!-- Submit button -->
    <button
      @click="fillContract"
      class="bg-green-600 px-4 py-2 text-white rounded-lg hover:bg-green-700"
    >
      Download PDF
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { PDFDocument, rgb } from "pdf-lib";

// Props
const props = defineProps({
  src: { type: String, required: true },
  data: { type: Object, required: true },
  fieldMap: { type: Object, required: true },
  fileName: { type: String, default: "filled-contract.pdf" },
  signature: { type: [String, File], default: null }, // prop can be File or Data URL
});

const signature = ref(null);

// Computed preview: Data URL or string
const signaturePreview = computed(() => {
  if (!signature.value) return null;
  if (typeof signature.value === "string") return signature.value;
  // If still a File, generate preview URL
  return URL.createObjectURL(signature.value);
});

// Handle signature upload
const handleSignatureUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    signature.value = file; // store File, convert later
  }
};

// Fill PDF and download
const fillContract = async () => {
  const existingPdfBytes = await fetch(props.src).then((res) =>
    res.arrayBuffer()
  );
  const pdfDoc = await PDFDocument.load(existingPdfBytes);
  const pages = pdfDoc.getPages();

  // Fill text fields
  Object.entries(props.data).forEach(([key, value]) => {
    if (props.fieldMap[key]) {
      const { page, x, y, size = 12 } = props.fieldMap[key];
      let safeValue = value ?? "";
      if (typeof safeValue !== "string") safeValue = String(safeValue);

      pages[page].drawText(safeValue, {
        x,
        y,
        size,
        color: rgb(0, 0, 0),
      });
    }
  });

  // Prepare signature
  let sig = signature.value ?? props.signature;

  if (sig instanceof File) {
    sig = await new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = (e) => resolve(e.target.result);
      reader.onerror = reject;
      reader.readAsDataURL(sig);
    });
  }

  if (typeof sig === "string" && props.fieldMap.signature) {
    try {
      const { page, x, y, w, h } = props.fieldMap.signature;
      let image;
      if (sig.startsWith("data:image/png")) image = await pdfDoc.embedPng(sig);
      else if (
        sig.startsWith("data:image/jpeg") ||
        sig.startsWith("data:image/jpg")
      )
        image = await pdfDoc.embedJpg(sig);

      if (image) pages[page].drawImage(image, { x, y, width: w, height: h });
    } catch (err) {
      console.error("Invalid signature image:", err);
    }
  }

  // Save and download
  const pdfBytes = await pdfDoc.save();
  const blob = new Blob([pdfBytes], { type: "application/pdf" });
  const url = URL.createObjectURL(blob);

  const a = document.createElement("a");
  a.href = url;
  a.download = props.fileName;
  a.click();
  URL.revokeObjectURL(url);
};

defineExpose({ fillContract, handleSignatureUpload });
</script>
