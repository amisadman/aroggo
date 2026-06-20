<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import { reactive, ref, nextTick, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert.vue";

const props = defineProps({
    metrics: Object,
    users: Array,
    leaderboard: Array,
    purchaseHistory: Array,
    prescriptions: Array,
    recentSales: Array,
});

const discountInputs = reactive({});

const staffForm = useForm({
    name: "",
    email: "",
    password: "",
    role: "data_entry",
});

const submitStaffForm = () => {
    staffForm.post(route("users.store-staff"), {
        onSuccess: () => {
            staffForm.reset("name", "email", "password");
        },
        preserveScroll: true,
    });
};

// Populate discount values from props
watch(() => props.leaderboard, (newVal) => {
    if (newVal) {
        newVal.forEach(cust => {
            if (discountInputs[cust.id] === undefined) {
                discountInputs[cust.id] = cust.next_purchase_discount || 0;
            }
        });
    }
}, { immediate: true });

const assignDiscount = (userId) => {
    const discountVal = discountInputs[userId] || 0;
    Inertia.post(route('customer.assign-discount', userId), { 
        discount: discountVal 
    }, {
        preserveScroll: true,
    });
};

const deleteUser = (userId) => {
    if (confirm("Are you sure you want to delete this user? This will also delete any associated records.")) {
        Inertia.delete(route('users.destroy', userId), {
            preserveScroll: true
        });
    }
};

const selectedSaleForReceipt = ref(null);

const openReceipt = (sale) => {
    selectedSaleForReceipt.value = sale;
};

const closeReceipt = () => {
    selectedSaleForReceipt.value = null;
};

const printReceipt = () => {
    nextTick(() => {
        window.print();
    });
};

const downloadInvoice = (sale) => {
    if (!sale) return;
    const invNumber = sale.id.toString().padStart(6, '0');
    const dateStr = new Date(sale.created_at).toLocaleString();
    const customerName = sale.customer ? sale.customer.name : 'Walk-in Guest';
    const customerEmail = sale.customer ? ` (${sale.customer.email})` : '';
    const medName = sale.medicine ? sale.medicine.name : 'Deleted Medicine';
    const compName = sale.medicine && sale.medicine.company ? `by ${sale.medicine.company.name}` : '';
    const qty = sale.quantity;
    const unitPrice = (sale.total_price / qty).toFixed(2);
    const totalPrice = parseFloat(sale.total_price).toFixed(2);

    let text = "";
    text += "========================================\n";
    text += "           AROGGO PHARMACY              \n";
    text += "    123 Health Ave, Medical District    \n";
    text += "        Phone: +1 (555) 019-2834        \n";
    text += "========================================\n\n";
    text += `Invoice #: INV-${invNumber}\n`;
    text += `Date:      ${dateStr}\n`;
    text += `Customer:  ${customerName}${customerEmail}\n`;
    text += "----------------------------------------\n";
    text += "Item                      Qty   Price    Total\n";
    text += "----------------------------------------\n";
    
    const itemLabel = medName.padEnd(25, ' ').substring(0, 25);
    const qtyStr = qty.toString().padStart(3, ' ');
    const priceStr = `$${unitPrice}`.padStart(7, ' ');
    const totalStr = `$${totalPrice}`.padStart(8, ' ');
    text += `${itemLabel} ${qtyStr} ${priceStr} ${totalStr}\n`;
    
    if (compName) {
        text += `(${compName})\n`;
    }
    text += "----------------------------------------\n";
    text += `TOTAL PAID:                     $${totalPrice}\n\n`;
    text += "========================================\n";
    text += "       Thank you for your purchase!     \n";
    text += "          Keep healthy, stay safe.      \n";
    text += "========================================\n";

    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `invoice_INV-${invNumber}.txt`;
    link.click();
    URL.revokeObjectURL(url);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Top Role Notification Banner -->
        <div class="pt-6 px-4">
            <div class="p-4 mb-4 rounded-lg shadow-sm flex items-center justify-between border-l-4"
                 :class="[
                     $page.props.auth.user.role === 'admin' ? 'bg-red-50 border-red-500 text-red-800' : '',
                     $page.props.auth.user.role === 'customer' ? 'bg-green-50 border-green-500 text-green-800' : '',
                     $page.props.auth.user.role === 'data_entry' ? 'bg-indigo-50 border-indigo-500 text-indigo-800' : ''
                 ]">
                <div>
                    <h2 class="text-lg font-semibold capitalize">
                        {{ $page.props.auth.user.role === 'admin' ? 'Admin Portal' : ($page.props.auth.user.role === 'customer' ? 'Customer Portal' : 'Data Entry Operator Portal') }}
                    </h2>
                    <p class="text-xs opacity-75">
                        Logged in as: <span class="font-bold">{{ $page.props.auth.user.name }}</span> ({{ $page.props.auth.user.email }})
                    </p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm capitalize"
                      :class="[
                          $page.props.auth.user.role === 'admin' ? 'bg-red-200 text-red-800' : '',
                          $page.props.auth.user.role === 'customer' ? 'bg-green-200 text-green-800' : '',
                          $page.props.auth.user.role === 'data_entry' ? 'bg-indigo-200 text-indigo-800' : ''
                      ]">
                    {{ $page.props.auth.user.role === 'data_entry' ? 'Operator' : $page.props.auth.user.role }}
                </span>
            </div>

            <div v-if="$page.props.flash.message" class="w-full max-w-md mx-auto my-4 print:hidden">
                <Alert />
            </div>

            <!-- CUSTOMER DASHBOARD LAYOUT -->
            <div v-if="$page.props.auth.user.role === 'customer'">
                <!-- Customer Metrics -->
                <div class="mt-4 w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ metrics.purchases_count }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Purchases</h3>
                            </div>
                            <div class="p-3 bg-indigo-50 rounded-full text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100 bg-gradient-to-br from-white to-green-50/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-extrabold text-green-700">${{ metrics.total_spent }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Total Expenses</h3>
                            </div>
                            <div class="p-3 bg-green-50 rounded-full text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ metrics.prescriptions_pending }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Pending Prescriptions</h3>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-full text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-green-600">{{ metrics.prescriptions_approved }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Approved Prescriptions</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-full text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Discount Notice if any -->
                <div v-if="$page.props.auth.user.next_purchase_discount > 0" class="mt-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center space-x-3 text-indigo-800">
                    <svg class="w-8 h-8 text-indigo-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm-2 4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-bold text-base">You have an active next-purchase discount!</h4>
                        <p class="text-xs">Your next purchase will automatically apply a <strong>{{ $page.props.auth.user.next_purchase_discount }}% discount</strong> at the register.</p>
                    </div>
                </div>

                <!-- Purchase History & Prescriptions -->
                <div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Purchase History -->
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Purchase Registry</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Inv #</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Medicine</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-500 uppercase tracking-wider text-xs">Qty</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">Total</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="sale in purchaseHistory" :key="sale.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-semibold text-gray-950">#{{ sale.id }}</td>
                                        <td class="px-4 py-3">
                                            <span class="font-medium text-gray-900 block">{{ sale.medicine ? sale.medicine.name : 'Deleted Medicine' }}</span>
                                            <span v-if="sale.medicine && sale.medicine.company" class="text-[10px] text-gray-400 block">{{ sale.medicine.company.name }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ sale.quantity }}</td>
                                        <td class="px-4 py-3 text-right text-green-700 font-bold">${{ sale.total_price }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <button 
                                                @click="openReceipt(sale)"
                                                class="px-2 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 text-xs font-semibold inline-flex items-center space-x-1"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                                <span>Invoice</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="purchaseHistory.length === 0">
                                        <td colspan="5" class="text-center py-6 text-gray-400 italic">No purchase history found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Prescriptions Uploaded -->
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Uploaded Prescriptions</h3>
                            <Link 
                                :href="route('prescription.create')"
                                class="px-3 py-1.5 bg-green-600 text-white font-semibold text-xs rounded hover:bg-green-700 inline-flex items-center space-x-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Upload New</span>
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Date</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Doctor</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Status</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">Prescription File</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="pres in prescriptions" :key="pres.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-xs text-gray-500">{{ new Date(pres.created_at).toLocaleDateString() }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-800">Dr. {{ pres.doctor_name }}</td>
                                        <td class="px-4 py-3 text-xs">
                                            <span 
                                                class="px-2 py-0.5 font-bold rounded-full uppercase text-[10px]"
                                                :class="pres.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'"
                                            >
                                                {{ pres.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a 
                                                v-if="pres.file_path"
                                                :href="`/storage/${pres.file_path}`" 
                                                target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs inline-flex items-center space-x-1"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <span>View Attachment</span>
                                            </a>
                                            <span v-else class="text-xs text-gray-400 italic">No File</span>
                                        </td>
                                    </tr>
                                    <tr v-if="prescriptions.length === 0">
                                        <td colspan="4" class="text-center py-6 text-gray-400 italic">No prescriptions uploaded yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAFF (ADMIN/OPERATOR) DASHBOARD LAYOUT -->
            <div v-else>
                <!-- Global Database Metrics -->
                <div class="mt-4 w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <!-- Medicines Card -->
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ metrics.medicines }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Total Medicines</h3>
                            </div>
                            <div class="p-3 bg-teal-50 rounded-full text-teal-655">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Card -->
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ metrics.sales }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Sales Logged</h3>
                            </div>
                            <div class="p-3 bg-pink-50 rounded-full text-pink-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Card -->
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow bg-gradient-to-br from-white to-green-50/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-extrabold text-green-700">${{ metrics.sales_revenue }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Total Revenue</h3>
                            </div>
                            <div class="p-3 bg-green-50 rounded-full text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Card -->
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ metrics.categories }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Categories</h3>
                            </div>
                            <div class="p-3 bg-green-50 rounded-full text-green-500">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Locations Card -->
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ metrics.locations }}</span>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mt-1">Location Racks</h3>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-full text-blue-500">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin & Data Entry Tables -->
                <div class="mt-8 grid grid-cols-1 gap-8">
                    <!-- Customer Spending Leaderboard (Visible to Admin & Operator, discount input editable only by admin) -->
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Customer Expenses Leaderboard</h3>
                            <p class="text-sm text-gray-500">List of registered customer accounts sorted by their total medical expenditures.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Customer Name</th>
                                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Email Address</th>
                                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">Total Expenses</th>
                                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider text-xs">Active Discount</th>
                                        <th v-if="$page.props.auth.user.role === 'admin'" class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider text-xs">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="cust in leaderboard" :key="cust.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ cust.name }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ cust.email }}</td>
                                        <td class="px-6 py-4 text-right font-extrabold text-green-700 text-base">
                                            ${{ parseFloat(cust.total_spent || 0).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold">
                                            <span 
                                                v-if="cust.next_purchase_discount > 0" 
                                                class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold"
                                            >
                                                {{ cust.next_purchase_discount }}% Off
                                            </span>
                                            <span v-else class="text-gray-400 italic text-xs">None</span>
                                        </td>
                                        <td v-if="$page.props.auth.user.role === 'admin'" class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <input 
                                                    type="number" 
                                                    v-model="discountInputs[cust.id]"
                                                    min="0"
                                                    max="100"
                                                    placeholder="%"
                                                    class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                                />
                                                <button 
                                                    @click="assignDiscount(cust.id)"
                                                    class="px-3 py-1 bg-indigo-600 text-white rounded text-xs font-bold hover:bg-indigo-700"
                                                >
                                                    Apply
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="leaderboard.length === 0">
                                        <td colspan="5" class="text-center py-6 text-gray-400 italic">No customer profiles found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Sales Transactions Table -->
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Recent Sales Transactions</h3>
                                <p class="text-sm text-gray-500">The 5 most recent sales completed in the pharmacy.</p>
                            </div>
                            <Link 
                                :href="route('sale.index')"
                                class="px-3 py-1.5 bg-[#1f2b5b] hover:bg-[#3dbdec] text-white font-semibold text-xs rounded transition-colors inline-flex items-center space-x-1"
                            >
                                <span>View All Sales</span>
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Inv #</th>
                                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Customer</th>
                                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-xs">Medicine</th>
                                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider text-xs">Qty</th>
                                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">Total</th>
                                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider text-xs">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="sale in recentSales" :key="sale.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-semibold text-gray-950">#{{ sale.id }}</td>
                                        <td class="px-6 py-4">
                                            <span class="font-medium text-gray-900 block">{{ sale.customer ? sale.customer.name : 'Walk-in Guest' }}</span>
                                            <span v-if="sale.customer" class="text-[10px] text-gray-400 block">{{ sale.customer.email }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-medium text-gray-900 block">{{ sale.medicine ? sale.medicine.name : 'Deleted Medicine' }}</span>
                                            <span v-if="sale.medicine && sale.medicine.company" class="text-[10px] text-gray-400 block">{{ sale.medicine.company.name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-gray-700">{{ sale.quantity }}</td>
                                        <td class="px-6 py-4 text-right text-green-700 font-bold">${{ parseFloat(sale.total_price).toFixed(2) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <button 
                                                @click="openReceipt(sale)"
                                                class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 text-xs font-bold inline-flex items-center space-x-1 border border-indigo-200"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                                <span>Invoice</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="!recentSales || recentSales.length === 0">
                                        <td colspan="6" class="text-center py-6 text-gray-400 italic">No recent transactions found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Registered Users Directory Table -->
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Registered Users Directory</h3>
                            <p class="text-sm text-gray-500 font-medium">All accounts registered on the system database.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role Assigned</th>
                                        <th scope="col" v-if="$page.props.auth.user.role === 'admin'" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">#{{ user.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full uppercase tracking-wider shadow-sm border"
                                                  :class="[
                                                      user.role === 'admin' ? 'bg-red-100 text-red-800 border-red-200' : '',
                                                      user.role === 'data_entry' ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : '',
                                                      user.role === 'customer' ? 'bg-green-100 text-green-800 border-green-200' : ''
                                                  ]">
                                                {{ user.role === 'admin' ? 'Admin' : (user.role === 'customer' ? 'Customer' : 'Data Entry') }}
                                            </span>
                                        </td>
                                        <td v-if="$page.props.auth.user.role === 'admin'" class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <button 
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="deleteUser(user.id)"
                                                class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700 transition-colors"
                                            >
                                                Delete
                                            </button>
                                            <span v-else class="text-xs text-gray-400 italic">Self</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Create Staff Account Form (Visible to Admin Only) -->
                    <div v-if="$page.props.auth.user.role === 'admin'" class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Create Staff / Operator Account</h3>
                            <p class="text-sm text-gray-500">Register new system administrators or data entry operator employee accounts.</p>
                        </div>
                        
                        <form @submit.prevent="submitStaffForm" class="space-y-4 max-w-2xl">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label>
                                    <input 
                                        type="text" 
                                        v-model="staffForm.name" 
                                        required 
                                        placeholder="e.g. John Doe"
                                        class="w-full text-sm border-gray-300 rounded focus:ring-[#3dbdec] focus:border-[#3dbdec] dark:bg-gray-700 dark:text-white"
                                    />
                                    <span v-if="staffForm.errors.name" class="text-xs text-red-500 mt-1 block">{{ staffForm.errors.name }}</span>
                                </div>
                                
                                <!-- Email -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
                                    <input 
                                        type="email" 
                                        v-model="staffForm.email" 
                                        required 
                                        placeholder="e.g. john@aroggo.com"
                                        class="w-full text-sm border-gray-300 rounded focus:ring-[#3dbdec] focus:border-[#3dbdec] dark:bg-gray-700 dark:text-white"
                                    />
                                    <span v-if="staffForm.errors.email" class="text-xs text-red-500 mt-1 block">{{ staffForm.errors.email }}</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Password -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                                    <input 
                                        type="password" 
                                        v-model="staffForm.password" 
                                        required 
                                        placeholder="Min 8 characters"
                                        class="w-full text-sm border-gray-300 rounded focus:ring-[#3dbdec] focus:border-[#3dbdec] dark:bg-gray-700 dark:text-white"
                                    />
                                    <span v-if="staffForm.errors.password" class="text-xs text-red-500 mt-1 block">{{ staffForm.errors.password }}</span>
                                </div>
                                
                                <!-- Role -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role Type</label>
                                    <div class="flex items-center space-x-4 h-10">
                                        <button 
                                            type="button"
                                            @click="staffForm.role = 'data_entry'"
                                            class="px-4 py-2 border rounded-md text-xs font-bold transition-all focus:outline-none"
                                            :class="staffForm.role === 'data_entry' 
                                                ? 'border-[#1f2b5b] bg-[#1f2b5b]/5 text-[#1f2b5b]' 
                                                : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300'"
                                        >
                                            Data Entry Operator
                                        </button>
                                        <button 
                                            type="button"
                                            @click="staffForm.role = 'admin'"
                                            class="px-4 py-2 border rounded-md text-xs font-bold transition-all focus:outline-none"
                                            :class="staffForm.role === 'admin' 
                                                ? 'border-[#3dbdec] bg-[#3dbdec]/10 text-[#1f2b5b]' 
                                                : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300'"
                                        >
                                            Administrator
                                        </button>
                                    </div>
                                    <span v-if="staffForm.errors.role" class="text-xs text-red-500 mt-1 block">{{ staffForm.errors.role }}</span>
                                </div>
                            </div>
                            
                            <div class="pt-2 flex justify-end">
                                <button 
                                    type="submit" 
                                    :disabled="staffForm.processing"
                                    class="px-5 py-2.5 bg-[#1f2b5b] hover:bg-[#3dbdec] text-white text-sm font-bold rounded shadow transition-colors"
                                >
                                    {{ staffForm.processing ? 'Creating...' : 'Create Staff Account' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Operator Shortcuts -->
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Operator Shortcuts & Quick Entry</h3>
                        <p class="text-sm text-gray-500 mb-6">Select a shortcut below to quickly populate and expand the pharmacy system database.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Record a Sale (Primary) -->
                            <Link :href="route('sale.create')" class="flex items-center p-4 rounded-xl border border-dashed border-pink-300 hover:border-pink-500 hover:bg-pink-50/50 transition-colors group">
                                <div class="p-3 rounded-lg bg-pink-100 text-pink-600 mr-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-base font-bold text-gray-900 group-hover:text-pink-700 transition-colors">Record a Sale</span>
                                    <span class="text-xs text-gray-500">Log customer purchase and deduct stock.</span>
                                </div>
                            </Link>

                            <!-- Quick Medicine Add -->
                            <Link :href="route('medicine.create')" class="flex items-center p-4 rounded-xl border border-dashed border-teal-300 hover:border-teal-500 hover:bg-teal-50/50 transition-colors group">
                                <div class="p-3 rounded-lg bg-teal-100 text-teal-600 mr-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-base font-bold text-gray-900 group-hover:text-teal-700 transition-colors">Quick Add Medicine</span>
                                    <span class="text-xs text-gray-500">Register new medicine items.</span>
                                </div>
                            </Link>

                            <!-- Quick Category -->
                            <Link :href="route('category.create')" class="flex items-center p-4 rounded-xl border border-dashed border-green-300 hover:border-green-500 hover:bg-green-50/50 transition-colors group">
                                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-base font-bold text-gray-900 group-hover:text-green-700 transition-colors">Quick Add Category</span>
                                    <span class="text-xs text-gray-500">Insert a new category tag for products.</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Modal component included inside Dashboard for printing/downloading -->
            <div 
                v-if="selectedSaleForReceipt" 
                class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900 bg-opacity-60 print:bg-white print:p-0"
            >
                <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl dark:bg-gray-800 p-6 print:shadow-none print:w-full print:max-w-none print:p-0 print:border-0 border border-gray-150">
                    <div class="flex justify-end space-x-3 mb-4 print:hidden">
                        <button 
                            @click="downloadInvoice(selectedSaleForReceipt)"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium text-sm flex items-center space-x-1.5 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Download Invoice</span>
                        </button>
                        <button 
                            @click="printReceipt"
                            class="px-4 py-2 bg-[#1f2b5b] text-white rounded hover:bg-[#3dbdec] font-medium text-sm flex items-center space-x-1.5 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span>Print Invoice</span>
                        </button>
                        <button 
                            @click="closeReceipt"
                            class="px-3 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 text-sm transition-colors"
                        >
                            Close
                        </button>
                    </div>

                    <div id="print-area" class="text-gray-900 dark:text-gray-100 font-mono text-sm leading-relaxed p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded">
                        <div class="text-center border-b border-dashed border-gray-300 dark:border-gray-600 pb-4 mb-4">
                            <h2 class="text-lg font-bold tracking-widest uppercase">AROGGO PHARMACY</h2>
                            <p class="text-xs text-gray-500 mt-1">123 Health Ave, Medical District</p>
                        </div>
                        <div class="space-y-1 mb-4 text-xs border-b border-dashed border-gray-300 dark:border-gray-600 pb-3">
                            <div class="flex justify-between">
                                <span>Invoice #:</span>
                                <span class="font-bold">INV-{{ selectedSaleForReceipt.id.toString().padStart(6, '0') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Date:</span>
                                <span>{{ new Date(selectedSaleForReceipt.created_at).toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Customer:</span>
                                <span class="font-bold">{{ selectedSaleForReceipt.customer ? selectedSaleForReceipt.customer.name : 'Walk-in Guest' }}</span>
                            </div>
                        </div>
                        <table class="w-full text-xs mb-4">
                            <thead>
                                <tr class="border-b border-dashed border-gray-300 dark:border-gray-600 text-left">
                                    <th class="pb-1">Item</th>
                                    <th class="pb-1 text-center">Qty</th>
                                    <th class="pb-1 text-right">Price</th>
                                    <th class="pb-1 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-2 pr-1">
                                        <span class="font-semibold">{{ selectedSaleForReceipt.medicine ? selectedSaleForReceipt.medicine.name : 'Deleted Medicine' }}</span>
                                    </td>
                                    <td class="py-2 text-center">{{ selectedSaleForReceipt.quantity }}</td>
                                    <td class="py-2 text-right">${{ (selectedSaleForReceipt.total_price / selectedSaleForReceipt.quantity).toFixed(2) }}</td>
                                    <td class="py-2 text-right">${{ parseFloat(selectedSaleForReceipt.total_price).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="border-t border-dashed border-gray-300 dark:border-gray-600 pt-3 space-y-1 text-xs">
                            <div class="flex justify-between font-bold text-sm">
                                <span>TOTAL PAID:</span>
                                <span>${{ parseFloat(selectedSaleForReceipt.total_price).toFixed(2) }}</span>
                            </div>
                            <div class="text-[10px] text-gray-400 mt-2 text-center italic">
                                Thank you for your purchase!<br>
                                Keep healthy, stay safe.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none !important;
        padding: 0 !important;
        background: white !important;
        color: black !important;
    }
}
</style>
