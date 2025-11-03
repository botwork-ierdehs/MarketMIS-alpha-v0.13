<template>
  <div class="space-y-6">
    <!-- Required documents list -->
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-secondary">Required Documents</h3>
      <ul class="space-y-2">
        <li 
          v-for="doc in requiredDocs" 
          :key="doc.id"
          class="flex items-center justify-between p-3 border rounded-lg bg-gray-50"
        >
          <div>
            <p class="text-sm font-medium text-gray-900">
              {{ `${doc.name} (${doc?.isRequired ? 'Required' : 'Optional'})` }}
            </p>
            <p class="text-xs text-gray-500">{{ doc.description }}</p>
          </div>
          <div 
            class="w-5 h-5 rounded-full flex items-center justify-center"
            :class="isDocumentUploaded(doc.id)
              ? 'bg-green-500 text-white'
              : 'bg-gray-200 text-gray-500'"
          >
            <svg 
              v-if="isDocumentUploaded(doc.id)" 
              class="w-3 h-3" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" 
                d="M16.707 5.293a1 1 0 010 1.414l-8 
                   8a1 1 0 01-1.414 0l-4-4a1 1 0 
                   011.414-1.414L8 12.586l7.293-7.293a1 
                   1 0 011.414 0z" 
                clip-rule="evenodd"/>
            </svg>
          </div>
        </li>
      </ul>
    </div>

    <!-- Upload area -->
    <div 
      class="p-6 border-2 border-dashed rounded-lg text-center bg-white hover:bg-gray-50 cursor-pointer"
      @dragover.prevent 
      @drop.prevent="handleDrop"
    >
      <div class="flex flex-col items-center space-y-2">
        <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" 
             viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" 
                d="M12 16v-8m0 0l-3 3m3-3l3 3m6 2v4a2 2 0 01-2 
                   2H6a2 2 0 01-2-2v-4m16-4l-4-4a2 2 0 
                   00-2-2H6a2 2 0 00-2 2v4" />
        </svg>
        <p class="text-sm text-gray-600">Upload Required Documents</p>
        <p class="text-xs text-gray-400">PDF, JPG, PNG (Max 5MB)</p>
        <input 
          type="file" 
          multiple 
          class="hidden" 
          ref="fileInput" 
          @change="handleFileSelect" 
          :accept="acceptedFileTypes"
        />
        <button 
          @click="$refs.fileInput.click()" 
          class="px-4 py-2 text-sm text-white bg-primary rounded-lg hover:bg-primary/60"
        >
          Choose Files
        </button>
      </div>
    </div>

    <!-- Uploaded files -->
    <div class="space-y-2">
      <div 
        v-for="file in uploadedFiles" 
        :key="file.id"
        class="p-3 border rounded-lg bg-gray-50 space-y-1"
      >
        <div class="flex items-center justify-between">
          <div class="flex-1 truncate">
            <p class="text-sm text-gray-800 truncate">{{ file.name }}</p>
            <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <span v-if="file.status === 'uploading'" class="text-xs text-primary">Uploading...</span>
            <span v-else-if="file.status === 'uploaded'" class="text-xs text-green-600">Uploaded</span>
            <span v-else-if="file.status === 'error'" class="text-xs text-red-600">Failed</span>
            <button 
              @click="removeFile(file.id)" 
              class="text-xs text-red-500 hover:underline"
            >
              Remove
            </button>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
          <div 
            class="h-2 bg-blue-500 transition-all duration-300"
            :class="{'bg-green-500': file.status === 'uploaded'}"
            :style="{ width: file.progress + '%' }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DocumentUploader",
  props: {
    requiredDocs: Array
  },
  data() {
    return {
      uploadedFiles: [],
      acceptedFileTypes: ".pdf,.jpg,.jpeg,.png"
    }
  },
  methods: {
    // handle drag and select
    handleDrop(e) {
      this.handleFiles(e.dataTransfer.files)
    },
    handleFileSelect(e) {
      this.handleFiles(e.target.files)
    },

    // handle file list (multi-upload support)
    handleFiles(files) {
      Array.from(files).forEach((file) => {
        if (this.validateFile(file)) {
          const fileObj = {
            id: Date.now() + Math.random(),
            name: file.name,
            size: file.size,
            type: file.type,
            file,
            status: "uploading",
            progress: 0,
            // 🔹 Match doc by order, or auto-null if no direct link
            documentType: this.assignDocumentId(file)
          }

          // keep old files, update only this one
          this.uploadedFiles.push(fileObj)
          this.simulateUpload(fileObj)
        }
      })
    },

    // match each uploaded file to a doc
    assignDocumentId(file) {
      // try to match by filename (if similar to doc name)
      const matched = this.requiredDocs.find(doc =>
        file.name.toLowerCase().includes(doc.name.toLowerCase())
      )
      // fallback: assign first doc without uploaded file
      if (matched) return matched.id
      const notUsed = this.requiredDocs.find(
        d => !this.uploadedFiles.some(f => f.documentType === d.id)
      )
      return notUsed ? notUsed.id : null
    },

    validateFile(file) {
      const maxSize = 5 * 1024 * 1024
      const validTypes = ["application/pdf", "image/jpeg", "image/png"]
      if (!validTypes.includes(file.type)) {
        alert("Invalid file type: " + file.name)
        return false
      }
      if (file.size > maxSize) {
        alert("File too large: " + file.name)
        return false
      }
      return true
    },

    // simulate upload with independent progress per file
    simulateUpload(fileObj) {
      const steps = [10, 30, 60, 90, 100]
      steps.forEach((value, i) => {
        setTimeout(() => {
          const index = this.uploadedFiles.findIndex(f => f.id === fileObj.id)
          if (index !== -1) {
            const updated = { ...this.uploadedFiles[index] }
            updated.progress = value
            updated.status = value === 100 ? "uploaded" : "uploading"

            // reassign so Vue detects change
            this.uploadedFiles.splice(index, 1, updated)
            this.$emit("update:files", [...this.uploadedFiles])
          }
        }, 400 * (i + 1))
      })
    },

    formatFileSize(size) {
      const kb = size / 1024
      return kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb / 1024).toFixed(1)} MB`
    },

    removeFile(id) {
      this.uploadedFiles = this.uploadedFiles.filter(f => f.id !== id)
      this.$emit("update:files", [...this.uploadedFiles])
    },

    isDocumentUploaded(docId) {
      return this.uploadedFiles.some(
        f => f.documentType === docId && f.status === "uploaded"
      )
    }
  }
}
</script>
