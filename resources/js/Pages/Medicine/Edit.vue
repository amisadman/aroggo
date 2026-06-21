<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import BackButton from "@/Components/BackButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/forms/TextInput.vue";
import Label from "@/Components/forms/Label.vue";

const props = defineProps({
    medicine: Object,
    companies: Array,
    categories: Array,
    locations: Array,
});

const form = useForm({
    name: props.medicine.name,
    company_id: props.medicine.company_id,
    category_id: props.medicine.category_id,
    location_id: props.medicine.location_id,
    pack_details: props.medicine.pack_details,
    quantity: props.medicine.quantity,
    price: props.medicine.price,
    status: props.medicine.status == 1,
});

const submit = () => {
    form.put(route("medicine.update", props.medicine.id));
};
</script>

<template>
    <Head title="Edit Medicine" />
    <AuthenticatedLayout>
        <div
            class="w-full max-w-2xl bg-white border mx-auto my-5 border-gray-200 rounded-lg shadow-md sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700"
        >
            <form class="space-y-6" @submit.prevent="submit">
                <h5 class="text-xl font-bold text-gray-900 dark:text-white flex items-center justify-between">
                    Edit Medicine Entry
                    <BackButton :url="route('medicine.index')" />
                </h5>

                <!-- Name & Pack Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <Label value="Medicine Name" id="name" />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <Label value="Pack Details" id="pack_details" />
                        <TextInput
                            id="pack_details"
                            type="text"
                            v-model="form.pack_details"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.pack_details" />
                    </div>
                </div>

                <!-- Quantity, Price & Company -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <Label value="Stock Quantity" id="quantity" />
                        <TextInput
                            id="quantity"
                            type="number"
                            v-model="form.quantity"
                            required
                            min="0"
                        />
                        <InputError class="mt-2" :message="form.errors.quantity" />
                    </div>
                    <div>
                        <Label value="Unit Price ($)" id="price" />
                        <TextInput
                            id="price"
                            type="number"
                            step="0.01"
                            v-model="form.price"
                            required
                            min="0"
                        />
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>
                    <div>
                        <Label value="Manufacturer Company" id="company_id" />
                        <select
                            id="company_id"
                            v-model="form.company_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"
                            required
                        >
                            <option value="" disabled>Select Company</option>
                            <option v-for="comp in companies" :key="comp.id" :value="comp.id">
                                {{ comp.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.company_id" />
                    </div>
                </div>

                <!-- Category & Storage Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <Label value="Category" id="category_id" />
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"
                            required
                        >
                            <option value="" disabled>Select Category</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.category_id" />
                    </div>
                    <div>
                        <Label value="Storage Location Rack" id="location_id" />
                        <select
                            id="location_id"
                            v-model="form.location_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"
                            required
                        >
                            <option value="" disabled>Select Rack</option>
                            <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                                {{ loc.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.location_id" />
                    </div>
                </div>

                <!-- Status Checkbox -->
                <div class="flex items-center">
                    <input
                        id="status"
                        type="checkbox"
                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
                        v-model="form.status"
                    />
                    <Label value="Publish / Active Status" id="status" customClass="ml-2" />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full text-white bg-[#1f2b5b] hover:bg-[#3dbdec] transition-colors focus:ring-2 focus:ring-[#3dbdec] font-semibold rounded-lg text-sm px-5 py-2.5 text-center shadow-md"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Update Medicine
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
