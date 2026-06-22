<script setup>
import { reactive, ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert.vue";
import Paginate from "@/Components/Paginate.vue";
import Icon from "@/Components/Icon.vue";

const props = defineProps({
    prescriptions: Object,
    filters: Object,
});

const search = ref(props.filters?.search || "");

watch(search, (value) => {
    Inertia.get(
        route("prescription.index"),
        { search: value },
        { preserveState: true, replace: true }
    );
});

const changeStatus = (presId, currentStatus) => {
    const newStatus = currentStatus === 'pending' ? 'approved' : 'pending';
    Inertia.put(route('prescription.update', presId), { 
        status: newStatus 
    }, {
        preserveScroll: true
    });
};

const deletePrescription = (presId) => {
    if (confirm("Are you sure you want to delete this prescription?")) {
        Inertia.delete(route('prescription.destroy', presId), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Prescriptions" />
    <AuthenticatedLayout>
        <div class="p-2 my-4">
            <div
                v-if="$page.props.flash.message"
                class="w-full max-w-md mx-auto"
            >
                <Alert />
            </div>
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <div
                    class="p-4 bg-white dark:bg-gray-900 flex flex-wrap items-center justify-between gap-3 border-b border-gray-100"
                >
                    <div class="flex flex-wrap items-center gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Customer Prescriptions</h3>
                            <p class="text-xs text-gray-500">
                                {{ $page.props.auth.user.role === 'customer' ? 'Manage your uploaded medical prescriptions.' : 'Review and approve customer uploaded prescriptions.' }}
                            </p>
                        </div>
                        <!-- Search input box -->
                        <div class="relative min-w-[280px]">
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none text-gray-400">
                                🔍
                            </div>
                            <input
                                type="text"
                                v-model="search"
                                class="block p-2 pl-9 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search by customer name, email, doctor..."
                            />
                        </div>
                    </div>
                    <Link
                        v-if="$page.props.auth.user.role === 'customer'"
                        :href="route('prescription.create')"
                        type="button"
                        class="text-white bg-[#1f2b5b] hover:bg-[#3dbdec] transition-colors focus:ring-2 focus:ring-[#3dbdec] font-semibold rounded-lg text-sm px-5 py-2.5 shadow-sm"
                    >
                        Upload Prescription
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
                            <th v-if="$page.props.auth.user.role !== 'customer'" scope="col" class="py-3 px-6">Customer</th>
                            <th scope="col" class="py-3 px-6">Doctor Name</th>
                            <th scope="col" class="py-3 px-6">Instructions</th>
                            <th scope="col" class="py-3 px-6">Uploaded At</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                            <th scope="col" class="py-3 px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            v-for="pres in prescriptions.data"
                            :key="pres.id"
                        >
                            <th
                                scope="row"
                                class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                #{{ pres.id }}
                            </th>
                            <td v-if="$page.props.auth.user.role !== 'customer'" class="py-4 px-6 font-semibold text-indigo-700 dark:text-indigo-400">
                                {{ pres.user ? pres.user.name : 'Unknown User' }}
                                <span class="block text-[10px] text-gray-400">{{ pres.user ? pres.user.email : '' }}</span>
                            </td>
                            <td class="py-4 px-6 font-medium">Dr. {{ pres.doctor_name }}</td>
                            <td class="py-4 px-6 text-xs max-w-xs truncate" :title="pres.instructions">
                                {{ pres.instructions || 'No instructions provided.' }}
                            </td>
                            <td class="py-4 px-6 text-xs">
                                {{ new Date(pres.created_at).toLocaleString() }}
                            </td>
                            <td class="py-4 px-6">
                                <span 
                                    class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm"
                                    :class="pres.status === 'approved' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                                >
                                    {{ pres.status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 flex items-center space-x-2">
                                <!-- View File Link -->
                                <a 
                                    v-if="pres.file_path"
                                    :href="`/storage/${pres.file_path}`" 
                                    target="_blank"
                                    class="bg-[#1f2b5b] hover:bg-[#3dbdec] p-1.5 rounded-md text-white font-semibold flex items-center justify-center transition-colors"
                                    title="View Attachment File"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <!-- Toggle Status Button (Staff Only) -->
                                <button
                                    v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'data_entry'"
                                    @click="changeStatus(pres.id, pres.status)"
                                    type="button"
                                    class="px-2 py-1.5 rounded-md text-white text-xs font-bold cursor-pointer"
                                    :class="pres.status === 'pending' ? 'bg-green-600 hover:bg-green-700' : 'bg-amber-600 hover:bg-amber-700'"
                                    :title="pres.status === 'pending' ? 'Approve Prescription' : 'Revert to Pending'"
                                >
                                    {{ pres.status === 'pending' ? 'Approve' : 'Unapprove' }}
                                </button>

                                <!-- Delete button (Admin or own Customer) -->
                                <button
                                    v-if="$page.props.auth.user.role === 'admin' || ($page.props.auth.user.role === 'customer' && pres.status === 'pending')"
                                    @click="deletePrescription(pres.id)"
                                    type="button"
                                    class="bg-red-600 hover:bg-red-800 p-1.5 rounded-md text-white font-semibold flex items-center justify-center"
                                    title="Delete Prescription"
                                >
                                    <Icon
                                        path="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                    />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="prescriptions.data.length === 0">
                            <td :colspan="$page.props.auth.user.role === 'customer' ? 6 : 7" class="text-center py-8 text-gray-400 italic">No prescriptions found.</td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-2">
                    <Paginate :data="prescriptions" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
