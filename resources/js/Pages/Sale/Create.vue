<script setup>
import { computed, ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import BackButton from "@/Components/BackButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/forms/TextInput.vue";
import Label from "@/Components/forms/Label.vue";

const props = defineProps({
    medicines: Array,
    customers: Array,
});

const form = useForm({
    medicine_id: "",
    customer_id: "",
    quantity: 1,
    unit_price: 0,
    total_price: 0,
});

const searchMedicineQuery = ref("");
const showMedicineDropdown = ref(false);

const filteredMedicines = computed(() => {
    const query = searchMedicineQuery.value.trim().toLowerCase();
    if (!query) return props.medicines;
    return props.medicines.filter((m) =>
        m.name.toLowerCase().includes(query) ||
        (m.location && m.location.name.toLowerCase().includes(query)) ||
        (m.category && m.category.name.toLowerCase().includes(query))
    );
});

const selectedMedicine = computed(() => {
    return props.medicines.find((m) => m.id === form.medicine_id);
});

const selectedCustomer = computed(() => {
    if (!form.customer_id) return null;
    return props.customers.find((c) => c.id === parseInt(form.customer_id));
});

const currentDiscount = computed(() => {
    return selectedCustomer.value ? selectedCustomer.value.next_purchase_discount : 0;
});

// Auto-calculate total price
watch(
    [() => form.quantity, () => form.unit_price, currentDiscount],
    ([qty, price, discount]) => {
        const base = qty * price;
        const final = base * (1 - discount / 100);
        form.total_price = final.toFixed(2);
    }
);

watch(searchMedicineQuery, (newVal) => {
    if (!newVal) {
        form.medicine_id = "";
    }
});

const selectMedicine = (med) => {
    form.medicine_id = med.id;
    form.unit_price = parseFloat(med.price || 0).toFixed(2);
    searchMedicineQuery.value = med.name;
    showMedicineDropdown.value = false;
};

const handleBlur = () => {
    // Slight delay to allow clicking options before dropdown closes
    setTimeout(() => {
        showMedicineDropdown.value = false;
    }, 200);
};

const submit = () => {
    form.post(route("sale.store"));
};
</script>

<template>
    <Head title="Record Sale" />
    <AuthenticatedLayout>
        <div
            class="w-full max-w-2xl bg-white border mx-auto my-5 border-gray-200 rounded-lg shadow-md sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700"
        >
            <form class="space-y-6" @submit.prevent="submit">
                <h5 class="text-xl font-bold text-gray-900 dark:text-white flex items-center justify-between">
                    Record Sales Transaction
                    <BackButton :url="route('sale.index')" />
                </h5>

                <!-- Select Customer -->
                <div>
                    <Label value="Link Customer (Optional)" id="customer_id" />
                    <select
                        id="customer_id"
                        v-model="form.customer_id"
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Guest (Anonymous Purchase)</option>
                        <option v-for="cust in customers" :key="cust.id" :value="cust.id">
                            {{ cust.name }} ({{ cust.email }})
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.customer_id" />

                    <!-- Discount Alert Banner -->
                    <div v-if="currentDiscount > 0" class="mt-2 text-sm text-green-700 font-semibold bg-green-50 px-3 py-2 rounded border border-green-200 flex justify-between items-center animate-pulse">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ currentDiscount }}% Next-Purchase Discount is active for this customer!</span>
                        </div>
                    </div>
                </div>

                <!-- Searchable Medicine Selection -->
                <div class="relative">
                    <Label value="Select Medicine" id="medicine_search" />
                    <div class="relative">
                        <input
                            id="medicine_search"
                            type="text"
                            v-model="searchMedicineQuery"
                            @focus="showMedicineDropdown = true"
                            @blur="handleBlur"
                            placeholder="Type to search medicine name, category, or location..."
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"
                            autocomplete="off"
                            required
                        />
                        <button 
                            type="button"
                            v-if="searchMedicineQuery"
                            @click="searchMedicineQuery = ''; form.medicine_id = '';"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            ✕
                        </button>
                    </div>
                    <input type="hidden" v-model="form.medicine_id" />
                    
                    <!-- Search Results Dropdown -->
                    <div 
                        v-if="showMedicineDropdown && filteredMedicines.length > 0"
                        class="absolute z-30 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg divide-y divide-gray-150 dark:divide-gray-700"
                    >
                        <div
                            v-for="med in filteredMedicines"
                            :key="med.id"
                            @mousedown="selectMedicine(med)"
                            class="p-3 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer flex flex-col md:flex-row md:justify-between md:items-center text-sm"
                        >
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ med.name }}</span>
                                <span v-if="med.category" class="ml-2 text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    {{ med.category.name }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-4 mt-1 md:mt-0 text-xs">
                                <span v-if="med.location" class="text-indigo-600 dark:text-indigo-400 font-medium">
                                    📍 {{ med.location.name }}
                                </span>
                                <span :class="med.quantity < 10 ? 'text-amber-600 font-bold' : 'text-gray-500'">
                                    Stock: {{ med.quantity }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <InputError class="mt-2" :message="form.errors.medicine_id" />
                    
                    <!-- Selected Medicine stock display and locations -->
                    <div v-if="selectedMedicine" class="mt-2 text-sm text-indigo-700 font-medium bg-indigo-50 px-3 py-2 rounded border border-indigo-100 flex flex-col md:flex-row md:justify-between md:items-center gap-1">
                        <div>
                            <span>Current Stock: <strong>{{ selectedMedicine.quantity }}</strong> items</span>
                            <span v-if="selectedMedicine.location" class="ml-3 text-indigo-900 bg-indigo-150 px-2 py-0.5 rounded text-xs">
                                Rack Location: <strong>{{ selectedMedicine.location.name }}</strong>
                            </span>
                        </div>
                        <span v-if="form.quantity > selectedMedicine.quantity" class="text-red-600 font-bold text-xs">Warning: Over stock limit!</span>
                    </div>
                </div>

                <!-- Quantity & Unit Price -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <Label value="Quantity to Sell" id="quantity" />
                        <TextInput
                            id="quantity"
                            type="number"
                            v-model="form.quantity"
                            required
                            min="1"
                            :max="selectedMedicine ? selectedMedicine.quantity : undefined"
                        />
                        <InputError class="mt-2" :message="form.errors.quantity" />
                    </div>
                    <div>
                        <Label value="Unit Price ($)" id="unit_price" />
                        <TextInput
                            id="unit_price"
                            type="number"
                            step="0.01"
                            v-model="form.unit_price"
                            required
                            min="0"
                        />
                        <InputError class="mt-2" :message="form.errors.unit_price" />
                    </div>
                </div>

                <!-- Computed Total Price -->
                <div>
                    <Label value="Total Price ($)" id="total_price" />
                    <div class="relative">
                        <input
                            id="total_price"
                            type="number"
                            step="0.01"
                            v-model="form.total_price"
                            class="w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed font-extrabold text-green-700 text-lg pl-3"
                            readonly
                        />
                        <span v-if="currentDiscount > 0" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded font-bold">
                            {{ currentDiscount }}% Off Applied
                        </span>
                    </div>
                    <InputError class="mt-2" :message="form.errors.total_price" />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full text-white bg-[#1f2b5b] hover:bg-[#3dbdec] transition-colors focus:ring-2 focus:ring-[#3dbdec] font-semibold rounded-lg text-sm px-5 py-2.5 text-center shadow-md"
                    :class="{ 
                        'opacity-25': form.processing || !form.medicine_id || (selectedMedicine && form.quantity > selectedMedicine.quantity),
                    }"
                    :disabled="form.processing || !form.medicine_id || (selectedMedicine && form.quantity > selectedMedicine.quantity)"
                >
                    Record Sale
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
