<script setup>
import { reactive, ref, nextTick } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Alert from "@/Components/Alert.vue";
import Paginate from "@/Components/Paginate.vue";
import Icon from "@/Components/Icon.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

defineProps({
    sales: Object,
});

const data = reactive({
    show: false,
    id: "",
});

const selectedSaleForReceipt = ref(null);

const destroy = (sale_id) => {
    data.show = true;
    data.id = sale_id;
};

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
    <Head title="Sales Register" />
    <AuthenticatedLayout>
        <DeleteModal
            v-model="data.show"
            :id="data.id"
            route="sale.destroy"
        />

        <!-- Receipt Print Modal -->
        <div 
            v-if="selectedSaleForReceipt" 
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900 bg-opacity-60 print:bg-white print:p-0"
        >
            <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl dark:bg-gray-800 p-6 print:shadow-none print:w-full print:max-w-none print:p-0 print:border-0 border border-gray-150">
                <!-- Close & Action Buttons (Hidden during printing) -->
                <div class="flex justify-end space-x-3 mb-4 print:hidden">
                    <button 
                        @click="printReceipt"
                        class="px-4 py-2 bg-[#1f2b5b] text-white rounded hover:bg-[#3dbdec] hover:text-[#1f2b5b] font-medium text-sm flex items-center space-x-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Print Invoice</span>
                    </button>
                    <button 
                        @click="downloadInvoice(selectedSaleForReceipt)"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium text-sm flex items-center space-x-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Download Invoice</span>
                    </button>
                    <button 
                        @click="closeReceipt"
                        class="px-3 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 text-sm"
                    >
                        Close
                    </button>
                </div>

                <!-- Printable Receipt Content -->
                <div id="print-area" class="text-gray-900 dark:text-gray-100 font-mono text-sm leading-relaxed p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded">
                    <div class="text-center border-b border-dashed border-gray-300 dark:border-gray-600 pb-4 mb-4">
                        <h2 class="text-lg font-bold tracking-widest uppercase">AROGGO PHARMACY</h2>
                        <p class="text-xs text-gray-500 mt-1">123 Health Ave, Medical District</p>
                        <p class="text-xs text-gray-500">Phone: +1 (555) 019-2834</p>
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
                        <div v-if="selectedSaleForReceipt.customer" class="flex justify-between">
                            <span>Email:</span>
                            <span class="text-[10px]">{{ selectedSaleForReceipt.customer.email }}</span>
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
                                    <span v-if="selectedSaleForReceipt.medicine && selectedSaleForReceipt.medicine.company" class="block text-[10px] text-gray-500">
                                        by {{ selectedSaleForReceipt.medicine.company.name }}
                                    </span>
                                </td>
                                <td class="py-2 text-center">{{ selectedSaleForReceipt.quantity }}</td>
                                <td class="py-2 text-right">${{ (selectedSaleForReceipt.total_price / selectedSaleForReceipt.quantity).toFixed(2) }}</td>
                                <td class="py-2 text-right">${{ selectedSaleForReceipt.total_price }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="border-t border-dashed border-gray-300 dark:border-gray-600 pt-3 space-y-1 text-xs">
                        <div class="flex justify-between font-bold text-sm">
                            <span>TOTAL PAID:</span>
                            <span>${{ selectedSaleForReceipt.total_price }}</span>
                        </div>
                        <div class="text-[10px] text-gray-400 mt-2 text-center italic">
                            Thank you for your purchase!<br>
                            Keep healthy, stay safe.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-2 my-4 print:hidden">
            <div
                v-if="$page.props.flash.message"
                class="w-full max-w-md mx-auto"
            >
                <Alert />
            </div>
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <div
                    class="p-4 bg-white dark:bg-gray-900 flex flex-wrap items-center justify-between"
                >
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Sales Transactions</h3>
                        <p class="text-xs text-gray-500">View logged sales records. Deleting a transaction refunds the stock.</p>
                    </div>
                    <Link
                        :href="route('sale.create')"
                        type="button"
                        class="text-white bg-[#1f2b5b] hover:bg-[#3dbdec] transition-colors focus:ring-2 focus:ring-[#3dbdec] font-semibold rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 shadow-sm"
                    >
                        Record a Sale
                    </Link>
                </div>
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="py-3 px-6">SI</th>
                            <th scope="col" class="py-3 px-6">Medicine Name</th>
                            <th scope="col" class="py-3 px-6">Company</th>
                            <th scope="col" class="py-3 px-6">Customer</th>
                            <th scope="col" class="py-3 px-6">Quantity Sold</th>
                            <th scope="col" class="py-3 px-6">Total Price</th>
                            <th scope="col" class="py-3 px-6">Transaction Date</th>
                            <th scope="col" class="py-3 px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            v-for="sale in sales.data"
                            :key="sale.id"
                        >
                            <th
                                scope="row"
                                class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                #{{ sale.id }}
                            </th>
                            <td class="py-4 px-6 font-semibold">
                                {{ sale.medicine ? sale.medicine.name : "Deleted Medicine" }}
                            </td>
                            <td class="py-4 px-6">
                                {{ sale.medicine && sale.medicine.company ? sale.medicine.company.name : "N/A" }}
                            </td>
                            <td class="py-4 px-6 font-medium text-indigo-700 dark:text-indigo-300">
                                {{ sale.customer ? sale.customer.name : 'Walk-in Guest' }}
                            </td>
                            <td class="py-4 px-6 font-bold">{{ sale.quantity }}</td>
                            <td class="py-4 px-6 text-green-600 font-extrabold">${{ sale.total_price }}</td>
                            <td class="py-4 px-6 text-xs">
                                {{ new Date(sale.created_at).toLocaleString() }}
                            </td>
                            <td class="py-4 px-6 flex items-center space-x-2">
                                <!-- Print Invoice Button -->
                                <button
                                    @click="openReceipt(sale)"
                                    type="button"
                                    class="bg-[#1f2b5b] hover:bg-[#3dbdec] px-2.5 py-2 rounded-md text-white font-semibold cursor-pointer flex items-center justify-center transition-colors"
                                    title="View & Print Invoice"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </button>

                                <!-- Delete button (Admin Only) -->
                                <button
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    @click="destroy(sale.id)"
                                    type="button"
                                    class="bg-red-600 hover:bg-red-800 px-2 py-1.5 rounded-md text-white font-semibold cursor-pointer flex items-center justify-center"
                                    title="Refund & Delete Sale"
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
                    <Paginate :data="sales" />
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
