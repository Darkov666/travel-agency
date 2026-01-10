<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, watch } from 'vue';

const form = useForm({
    razon_social: '',
    commercial_name: '',
    business_type: '',
    contact_email: '',
    contact_phone: '',
    rfc: '', // Added explicit RFC
    regimen_fiscal: '',
    creation_date: '',
    fiscal_address: '',
    
    rep_name: '',
    rep_curp: '',
    rep_ine: null,
    
    constancia_fiscal: null,
    proof_address: null,
    
    hosting_mode: 'subdomain',
    subdomain_slug: '',
    custom_domain: '',
    modules: ['transport'], // Default to transport
});

const submit = () => {
    form.post(route('partner.store'), {
        preserveScroll: true,
        onError: () => {
             // Handle errors
        }
    });
};

const handleFileUpload = (field, e) => {
    form[field] = e.target.files[0];
};

</script>

<template>
    <Head title="Register your Agency" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Partner with Cancun Sunny</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Launch your own travel agency platform in minutes.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        
                        <!-- 1. Business Info -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Business Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Commercial Name</label>
                                    <input v-model="form.commercial_name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required placeholder="e.g. My Travel Agency">
                                    <div v-if="form.errors.commercial_name" class="text-red-500 text-xs mt-1">{{ form.errors.commercial_name }}</div>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Types (Modules)</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" v-model="form.modules" value="transport" class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <span class="text-gray-700 dark:text-gray-300">Transportation (Transfers)</span>
                                        </label>
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" v-model="form.modules" value="tours" class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <span class="text-gray-700 dark:text-gray-300">Tours & Packages</span>
                                        </label>
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" v-model="form.modules" value="baggage" class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <span class="text-gray-700 dark:text-gray-300">Baggage Handling</span>
                                        </label>
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" v-model="form.modules" value="groups_lodging" class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <span class="text-gray-700 dark:text-gray-300">Groups & Lodging</span>
                                        </label>
                                    </div>
                                    <div v-if="form.errors.modules" class="text-red-500 text-xs mt-1">{{ form.errors.modules }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Type (Giro)</label>
                                    <input v-model="form.business_type" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required placeholder="e.g. Tours & Transfers">
                                    <div v-if="form.errors.business_type" class="text-red-500 text-xs mt-1">{{ form.errors.business_type }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Email</label>
                                    <input v-model="form.contact_email" type="email" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                    <div v-if="form.errors.contact_email" class="text-red-500 text-xs mt-1">{{ form.errors.contact_email }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Phone</label>
                                    <input v-model="form.contact_phone" type="tel" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                    <div v-if="form.errors.contact_phone" class="text-red-500 text-xs mt-1">{{ form.errors.contact_phone }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Legal Info -->
                         <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Legal Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Razón Social</label>
                                    <input v-model="form.razon_social" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                    <div v-if="form.errors.razon_social" class="text-red-500 text-xs mt-1">{{ form.errors.razon_social }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RFC</label>
                                    <input v-model="form.rfc" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                    <div v-if="form.errors.rfc" class="text-red-500 text-xs mt-1">{{ form.errors.rfc }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fiscal Regimen</label>
                                    <input v-model="form.regimen_fiscal" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Creation Date</label>
                                    <input v-model="form.creation_date" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fiscal Address</label>
                                    <input v-model="form.fiscal_address" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </div>
                            </div>
                        </div>

                         <!-- 3. Representative -->
                         <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Legal Representative</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                    <input v-model="form.rep_name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CURP</label>
                                    <input v-model="form.rep_curp" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required minlength="18" maxlength="18">
                                </div>
                            </div>
                        </div>

                        <!-- 4. Documents -->
                         <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Documents (PDF Only)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Constancia de Situación Fiscal</label>
                                    <input @change="handleFileUpload('constancia_fiscal', $event)" type="file" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300" required>
                                    <p class="text-xs text-gray-500 mt-1">Must be less than 3 months old.</p>
                                    <div v-if="form.errors.constancia_fiscal" class="text-red-500 text-xs mt-1">{{ form.errors.constancia_fiscal }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comprobante de Domicilio</label>
                                    <input @change="handleFileUpload('proof_address', $event)" type="file" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300" required>
                                      <div v-if="form.errors.proof_address" class="text-red-500 text-xs mt-1">{{ form.errors.proof_address }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">INE (Representative)</label>
                                    <input @change="handleFileUpload('rep_ine', $event)" type="file" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300" required>
                                    <div v-if="form.errors.rep_ine" class="text-red-500 text-xs mt-1">{{ form.errors.rep_ine }}</div>
                                </div>
                            </div>
                        </div>

                         <!-- 5. Platform Configuration -->
                         <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Platform Configuration</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hosting Mode</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="{'ring-2 ring-blue-500': form.hosting_mode === 'subdomain'}">
                                        <input type="radio" v-model="form.hosting_mode" value="subdomain" class="mr-2">
                                        <div>
                                            <div class="font-bold">Subdomain</div>
                                            <div class="text-sm text-gray-500">e.g. agency.cancunsunny.com</div>
                                            <div class="text-xs text-green-600 font-bold">$1000 MXN/mo</div>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="{'ring-2 ring-blue-500': form.hosting_mode === 'domain'}">
                                        <input type="radio" v-model="form.hosting_mode" value="domain" class="mr-2">
                                         <div>
                                            <div class="font-bold">Custom Domain</div>
                                            <div class="text-sm text-gray-500">e.g. myagency.com</div>
                                            <div class="text-xs text-green-600 font-bold">$1500 MXN/mo</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div v-if="form.hosting_mode === 'subdomain'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subdomain Slug</label>
                                <div class="flex">
                                    <input v-model="form.subdomain_slug" type="text" class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required placeholder="myagency">
                                    <span class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">.cancunsunny.com</span>
                                </div>
                                <div v-if="form.errors.subdomain_slug" class="text-red-500 text-xs mt-1">{{ form.errors.subdomain_slug }}</div>
                            </div>

                            <div v-if="form.hosting_mode === 'domain'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custom Domain</label>
                                <input v-model="form.custom_domain" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required placeholder="myagency.com">
                                <p class="text-xs text-gray-500 mt-1">You will need to configure your DNS records after registration.</p>
                                <div v-if="form.errors.custom_domain" class="text-red-500 text-xs mt-1">{{ form.errors.custom_domain }}</div>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 shadow-lg transform hover:scale-105 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ form.processing ? 'Processing...' : 'Continue to Payment' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
