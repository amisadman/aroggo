<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import BackButton from "@/Components/BackButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/forms/TextInput.vue";
import Label from "@/Components/forms/Label.vue";

const form = useForm({
    doctor_name: "",
    instructions: "",
    prescription_file: null,
});

const filePreview = ref(null);
const fileInput = ref(null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.prescription_file = file;
        
        // Show local preview if it is an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                filePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            filePreview.value = null; // PDFs or other files won't show image preview
        }
    }
};

const submit = () => {
    form.post(route("prescription.store"), {
        onSuccess: () => {
            form.reset();
            filePreview.value = null;
        }
    });
};
</script>

<template>
    <Head title="Upload Prescription" />
    <AuthenticatedLayout>
        <div
            class="w-full max-w-2xl bg-white border mx-auto my-5 border-gray-200 rounded-lg shadow-md sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700"
        >
            <form class="space-y-6" @submit.prevent="submit">
                <h5 class="text-xl font-bold text-gray-900 dark:text-white flex items-center justify-between">
                    Upload Doctor Prescription
                    <BackButton :url="route('prescription.index')" />
                </h5>

                <!-- Doctor Name -->
                <div>
                    <Label value="Doctor Name" id="doctor_name" />
                    <TextInput
                        id="doctor_name"
                        type="text"
                        v-model="form.doctor_name"
                        required
                        placeholder="e.g. Dr. John Doe"
                    />
                    <InputError class="mt-2" :message="form.errors.doctor_name" />
                </div>

                <!-- Instructions -->
                <div>
                    <Label value="Instructions / Notes (Optional)" id="instructions" />
                    <textarea
                        id="instructions"
                        v-model="form.instructions"
                        rows="3"
                        placeholder="Detail any specific instructions for medicine dosage or pharmacy operators..."
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.instructions" />
                </div>

                <!-- Prescription File selector -->
                <div>
                    <Label value="Upload File (Image or PDF)" id="prescription_file" />
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 dark:border-gray-600 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="file-upload" ref="fileInput" name="prescription_file" type="file" class="sr-only" @change="handleFileChange" accept=".jpg,.jpeg,.png,.pdf" required />
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, JPEG, or PDF up to 2MB
                            </p>
                        </div>
                    </div>
                    <div v-if="form.prescription_file" class="mt-2 text-xs text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded border border-indigo-100 flex justify-between items-center">
                        <span>Selected File: {{ form.prescription_file.name }}</span>
                        <button type="button" @click="form.prescription_file = null; filePreview = null; fileInput.value = '';" class="text-red-500 font-bold hover:text-red-700">Remove</button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.prescription_file" />
                </div>

                <!-- Preview Box -->
                <div v-if="filePreview" class="mt-4 border rounded p-2 bg-gray-50 dark:bg-gray-750 flex flex-col items-center">
                    <span class="text-xs font-semibold text-gray-500 mb-2">Prescription Scan Preview:</span>
                    <img :src="filePreview" class="max-h-64 max-w-full rounded shadow-sm" alt="Prescription preview" />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full text-white bg-[#1f2b5b] hover:bg-[#3dbdec] transition-colors focus:ring-2 focus:ring-[#3dbdec] font-semibold rounded-lg text-sm px-5 py-2.5 text-center shadow-md"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Submit Prescription
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
