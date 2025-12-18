import { ref } from 'vue'

export function useSubmitForm(endpoint = '/wp-json/contact/v1/send') {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const success = ref(false)
  const progress = ref(0) // <-- progress percentage 0-100

  /**
   * submitForm
   * @param formData - an object containing form fields. Files should be File or File[]
   */
  function submitForm(formData: Record<string, any>) {
    return new Promise<any>((resolve, reject) => {
      loading.value = true
      error.value = null
      success.value = false
      progress.value = 0

      const hasFiles = Object.values(formData).some(
        (v) => v instanceof File || (Array.isArray(v) && v.every((f) => f instanceof File))
      )

      let body: FormData | string

      if (hasFiles) {
        const multipart = new FormData()
        for (const key in formData) {
          const value = formData[key]
          if (value instanceof File) {
            multipart.append(key, value)
          } else if (Array.isArray(value) && value.every((v) => v instanceof File)) {
            value.forEach((file) => multipart.append(key + '[]', file))
          } else {
            multipart.append(key, value)
          }
        }
        body = multipart
      } else {
        body = JSON.stringify(formData)
      }

      // Use XMLHttpRequest for upload progress
      const xhr = new XMLHttpRequest()
      xhr.open('POST', endpoint, true)

      if (!hasFiles) {
        xhr.setRequestHeader('Content-Type', 'application/json')
      }

      xhr.upload.onprogress = (event) => {
        if (event.lengthComputable) {
          progress.value = Math.round((event.loaded / event.total) * 100)
        }
      }

      xhr.onload = () => {
        loading.value = false
        if (xhr.status >= 200 && xhr.status < 300) {
          success.value = true
          resolve(JSON.parse(xhr.responseText))
        } else {
          error.value = JSON.parse(xhr.responseText)?.message || 'Submission failed'
	  reject(new Error(error.value ?? 'Unknown error'))

        }
      }

      xhr.onerror = () => {
        loading.value = false
        error.value = 'Network error'
        reject(new Error(error.value))
      }

      xhr.send(body)
    })
  }

  return {
    submitForm,
    loading,
    error,
    success,
    progress, // <-- reactive progress value
  }
}

