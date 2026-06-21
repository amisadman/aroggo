<script setup>
import { reactive, ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import Alert from "@/Components/Alert.vue";
import Paginate from "@/Components/Paginate.vue";
import Icon from "@/Components/Icon.vue";
import DeleteModal from "@/Components/DeleteModal.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    medicines: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");

watch(search, (value) => {
    Inertia.get(
        route("medicine.index"),
        { search: value },
        { preserveState: true, replace: true }
    );
});

const data = reactive({
    show: false,
    id: "",
});

const destroy = (med_id) => {
    data.show = true;
    data.id = med_id;
};

// Stock Modal Setup
const stockState = reactive({
    show: false,
    id: "",
    name: "",
    quantity: 0,
});

const openStockModal = (med) => {
    stockState.id = med.id;
    stockState.name = med.name;
    stockState.quantity = med.quantity;
    stockState.show = true;
};

const stockForm = useForm({
    quantity: 0,
});

const saveStock = () => {
    stockForm.quantity = stockState.quantity;
    stockForm.post(route("medicine.update-stock", stockState.id), {
        onSuccess: () => {
            stockState.show = false;
        },
    });
};

// CSV Import Form
const csvForm = useForm({
    csv_file: null,
});

const fileInputRef = ref(null);

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const handleCsvUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        csvForm.csv_file = file;
        csvForm.post(route('medicine.import-csv'), {
            onSuccess: () => {
                csvForm.reset();
            },
        });
    }
};
</script>

<template>
    <Head title="Medicines" />
    <AuthenticatedLayout>
        <DeleteModal
            v-model="data.show"
            :id="data.id"
            route="medicine.destroy"
        />

        <!-- Stock Update Modal -->
        <div v-if="stockState.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Update Stock Quantity</h3>
                <p class="text-sm text-gray-500 mb-4">Adjust inventory level for: <span class="font-semibold text-gray-800">{{ stockState.name }}</span></p>
                <form @submit.prevent="saveStock">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1" for="stock-qty">Quantity in Stock</label>
                        <input
                            type="number"
                            id="stock-qty"
                            v-model="stockState.quantity"
                            min="0"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        />
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="stockState.show = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-750 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="stockForm.processing"
                             class="px-4 py-2 bg-[#1f2b5b] hover:bg-[#3dbdec] text-white rounded-md text-sm font-medium shadow-sm transition-colors"
                        >
                            Save Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-2 my-4">
            <div
                v-if="$page.props.flash.message"
                class="w-full max-w-md mx-auto"
            >
                <Alert />
            </div>
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <div
                    class="p-4 bg-white dark:bg-gray-900 flex flex-wrap items-center justify-between gap-3"
                >
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative mt-1">
                        <div
                            class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
                        >
                            <Icon
                                path="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                            />
                        </div>
                        <input
                            type="text"
                            id="table-search"
                            v-model="search"
                            class="block p-2 pl-10 w-80 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search by name, company, or category"
                        />
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- CSV File Uploader Form -->
                        <input 
                            type="file" 
                            ref="fileInputRef" 
                            @change="handleCsvUpload" 
                            accept=".csv" 
                            class="hidden" 
                        />
                        <button
                            @click="triggerFileInput"
                            type="button"
                            :disabled="csvForm.processing"
                            class="text-[#1f2b5b] border-2 border-[#1f2b5b] hover:bg-[#1f2b5b] hover:text-white transition-colors focus:ring-2 focus:ring-[#1f2b5b] font-semibold rounded-lg text-sm px-5 py-2.5 mr-2 mb-2"
                        >
                            {{ csvForm.processing ? 'Importing...' : 'Import CSV' }}
                        </button>

                        <Link
                            :href="route('medicine.create')"
                            type="button"
                            class="text-white bg-[#1f2b5b] hover:bg-[#3dbdec] transition-colors focus:ring-2 focus:ring-[#3dbdec] font-semibold rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 shadow-sm"
                        >
                            Create Medicine
                        </Link>
                    </div>
                </div>
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="py-3 px-4">SI</th>
                            <th scope="col" class="py-3 px-4">Name</th>
                            <th scope="col" class="py-3 px-4">Company</th>
                            <th scope="col" class="py-3 px-4">Category</th>
                            <th scope="col" class="py-3 px-4 text-indigo-700 font-extrabold bg-indigo-50/50">Location Rack (Storage)</th>
                            <th scope="col" class="py-3 px-4">Pack Details</th>
                            <th scope="col" class="py-3 px-4">Unit Price</th>
                            <th scope="col" class="py-3 px-4">Stock Qty</th>
                            <th scope="col" class="py-3 px-4">Status</th>
                            <th scope="col" class="py-3 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            v-for="med in medicines.data"
                            :key="med.id"
                        >
                            <th
                                scope="row"
                                class="py-4 px-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                {{ med.id }}
                            </th>
                            <td class="py-4 px-4 font-semibold">{{ med.name }}</td>
                            <td class="py-4 px-4">{{ med.company ? med.company.name : "N/A" }}</td>
                            <td class="py-4 px-4">{{ med.category ? med.category.name : "N/A" }}</td>
                            <!-- Highlighted Storage Location Rack -->
                            <td class="py-4 px-4 bg-indigo-50/50 font-bold text-indigo-800 border-l border-r border-indigo-100">
                                <span class="bg-indigo-100 px-2.5 py-1 rounded text-indigo-900 border border-indigo-200">
                                    {{ med.location ? med.location.name : "Unassigned" }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs">{{ med.pack_details }}</td>
                            <td class="py-4 px-4 font-extrabold text-green-700">${{ parseFloat(med.price || 0).toFixed(2) }}</td>
                            <td class="py-4 px-4">
                                <span :class="med.quantity < 10 ? 'text-red-600 font-extrabold bg-red-50 px-2 py-0.5 rounded' : 'font-semibold'">
                                    {{ med.quantity }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                {{
                                    med.status == 1
                                        ? "Published"
                                        : "Pending"
                                }}
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <!-- Update Stock Button (Shared) -->
                                <button
                                    @click="openStockModal(med)"
                                    type="button"
                                    class="bg-yellow-500 hover:bg-yellow-600 px-1.5 py-1.5 rounded-md text-white font-semibold cursor-pointer mr-1.5 inline-flex items-center"
                                    title="Adjust Stock"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </button>

                                <Link
                                    :href="route('medicine.edit', med.id)"
                                    as="button"
                                    class="bg-indigo-600 hover:bg-indigo-800 p-1.5 rounded-md text-white font-semibold cursor-pointer inline-flex items-center"
                                >
                                    <Icon
                                        path="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                    />
                                </Link>

                                <!-- Delete button (Admin Only) -->
                                <button
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    @click="destroy(med.id)"
                                    type="button"
                                    class="bg-red-600 hover:bg-red-800 p-1.5 mx-1.5 rounded-md text-white font-semibold cursor-pointer inline-flex items-center"
                                >
                                    <Icon
                                        path="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                    />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-2">
                    <Paginate :data="medicines" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
