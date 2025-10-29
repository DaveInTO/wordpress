import { ref } from 'vue';

export function useSubmitForm(endpoint = '/wp-json/contact/v1/send') {
  const loading = ref(false);
  const error = ref<string | null>(null);
  const success = ref(false);

  /**
   * submitForm
   * @param formData - an object containing form fields. Files should be File or File[]
   */
  async function submitForm(formData: Record<string, any>) {
    loading.value = true;
    error.value = null;
    success.value = false;

    try {
      let body: BodyInit;

      // Detect if any field is a File or File[]
      const hasFiles = Object.values(formData).some(
        (v) => v instanceof File || (Array.isArray(v) && v.every((f) => f instanceof File))
      );

      if (hasFiles) {
        const multipart = new FormData();
        for (const key in formData) {
          const value = formData[key];
          if (value instanceof File) {
            multipart.append(key, value);
          } else if (Array.isArray(value) && value.every((v) => v instanceof File)) {
            value.forEach((file) => multipart.append(key + '[]', file));
          } else {
            multipart.append(key, value);
          }
        }
        body = multipart;
      } else {
        body = JSON.stringify(formData);
      }

      const res = await fetch(endpoint, {
        method: 'POST',
        headers: hasFiles ? undefined : { 'Content-Type': 'application/json' },
        body,
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data?.message || 'Submission failed');
      }

      success.value = true;
      return data;
    } catch (err: any) {
      error.value = err.message || 'Unknown error';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    submitForm,
    loading,
    error,
    success,
  };
}

