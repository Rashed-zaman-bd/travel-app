<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm
             sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    Offer Shows
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage offer show slider items.
                </p>
            </div>

            <button type="button" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold
               text-white transition hover:bg-blue-700" @click="createOffer">
                <i class="bi bi-plus-lg mr-1"></i>
                Add Offer
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-6 rounded-xl bg-white p-4 shadow-sm">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Search -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Search
                    </label>

                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2
                     -translate-y-1/2 text-gray-400"></i>

                        <input v-model="search" type="text" placeholder="Search title or location..." class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3
                     text-sm outline-none transition
                     focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @input="filterOffers" />
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Status
                    </label>

                    <select v-model="status" class="w-full rounded-lg border border-gray-300 px-3 py-2.5
                   text-sm outline-none focus:border-blue-500
                   focus:ring-1 focus:ring-blue-500" @change="fetchOffers">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Refresh -->
                <div class="flex items-end">
                    <button type="button" class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   text-sm font-medium text-gray-700 transition
                   hover:bg-gray-50" @click="resetFilters">
                        <i class="bi bi-arrow-clockwise mr-1"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow-sm">
            <!-- Loading -->
            <div v-if="loading" class="flex min-h-[300px] items-center justify-center">
                <div class="text-center">
                    <div class="mx-auto h-8 w-8 animate-spin rounded-full
                   border-4 border-gray-200 border-t-blue-600"></div>

                    <p class="mt-3 text-sm text-gray-500">
                        Loading offers...
                    </p>
                </div>
            </div>

            <!-- Empty -->
            <div v-else-if="offers.length === 0" class="flex min-h-[300px] items-center justify-center px-4">
                <div class="text-center">
                    <i class="bi bi-inbox text-5xl text-gray-300"></i>

                    <h3 class="mt-3 text-base font-semibold text-gray-700">
                        No offers found
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        No offer show records are available.
                    </p>
                </div>
            </div>

            <!-- Desktop table -->
            <div v-else class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                #
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Image
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Offer
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Location
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Price
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Duration
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Discount
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Order
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(offer, index) in offers" :key="offer.id" class="transition hover:bg-gray-50">
                            <!-- Number -->
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                {{ index + 1 }}
                            </td>

                            <!-- Image -->
                            <td class="px-5 py-4">
                                <img v-if="offer.image" :src="offer.image" :alt="label(offer.title)"
                                    class="h-16 w-24 rounded-lg object-cover" />

                                <div v-else class="flex h-16 w-24 items-center justify-center
                         rounded-lg bg-gray-100 text-gray-400">
                                    <i class="bi bi-image text-xl"></i>
                                </div>
                            </td>

                            <!-- Offer -->
                            <td class="max-w-[220px] px-5 py-4">
                                <p class="font-semibold text-gray-800" :title="label(offer.title)">
                                    {{ label(offer.title) || "Untitled" }}
                                </p>

                                <p v-if="offer.description" class="mt-1 line-clamp-2 text-xs text-gray-500">
                                    {{ label(offer.description) }}
                                </p>
                            </td>

                            <!-- Location -->
                            <td class="px-5 py-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <i class="bi bi-geo-alt text-gray-400"></i>
                                    {{ label(offer.location) || "-" }}
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="whitespace-nowrap px-5 py-4">
                                <span v-if="offer.price !== null && offer.price !== undefined"
                                    class="font-semibold text-gray-800">
                                    {{ Number(offer.price).toLocaleString() }}
                                </span>

                                <span v-else class="text-gray-400">
                                    -
                                </span>
                            </td>

                            <!-- Duration -->
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ label(offer.tour_duration) || "-" }}
                            </td>

                            <!-- Discount -->
                            <td class="px-5 py-4">
                                <span v-if="label(offer.discount)" class="rounded-full bg-orange-50 px-2.5 py-1
                         text-xs font-semibold text-orange-600">
                                    {{ label(offer.discount) }}
                                </span>

                                <span v-else class="text-gray-400">
                                    -
                                </span>
                            </td>

                            <!-- Order -->
                            <td class="px-5 py-4 text-sm font-medium text-gray-600">
                                {{ offer.order }}
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                <span v-if="offer.is_active" class="rounded-full bg-green-50 px-2.5 py-1
                         text-xs font-semibold text-green-600">
                                    Active
                                </span>

                                <span v-else class="rounded-full bg-red-50 px-2.5 py-1
                         text-xs font-semibold text-red-600">
                                    Inactive
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" title="Edit" class="rounded-lg border border-blue-200
                           p-2 text-blue-600 transition
                           hover:bg-blue-50" @click="editOffer(offer)">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button type="button" title="Delete" class="rounded-lg border border-red-200
                           p-2 text-red-600 transition
                           hover:bg-red-50" @click="deleteOffer(offer)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="closeModal">
            <div class="max-h-[95vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white shadow-xl">
                <!-- Modal Header -->
                <div class="sticky top-0 z-10 flex items-center justify-between
             border-b bg-white px-6 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            {{ editingOffer ? "Edit Offer Show" : "Create Offer Show" }}
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            {{
                                editingOffer
                                    ? "Update offer show information."
                            : "Add a new offer show."
                            }}
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        @click="closeModal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveOffer" class="p-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <!-- English Title -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Title (English)
                            </label>

                            <input v-model="form.title.en" type="text" placeholder="Enter English title"
                                class="input" />
                        </div>

                        <!-- Bangla Title -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Title (Bangla)
                            </label>

                            <input v-model="form.title.bn" type="text" placeholder="বাংলা শিরোনাম" class="input" />
                        </div>

                        <!-- English Description -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Description (English)
                            </label>

                            <textarea v-model="form.description.en" rows="4" placeholder="Enter English description"
                                class="input"></textarea>
                        </div>

                        <!-- Bangla Description -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Description (Bangla)
                            </label>

                            <textarea v-model="form.description.bn" rows="4" placeholder="বাংলা বিবরণ"
                                class="input"></textarea>
                        </div>

                        <!-- English Location -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Location (English)
                            </label>

                            <input v-model="form.location.en" type="text" placeholder="Cox's Bazar" class="input" />
                        </div>

                        <!-- Bangla Location -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Location (Bangla)
                            </label>

                            <input v-model="form.location.bn" type="text" placeholder="কক্সবাজার" class="input" />
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Price
                            </label>

                            <input v-model="form.price" type="number" min="0" step="0.01" placeholder="15000"
                                class="input" />
                        </div>

                        <!-- Payment English -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Payment Method (English)
                            </label>

                            <input v-model="form.payment_method.en" type="text" placeholder="Pay in 3 installments"
                                class="input" />
                        </div>

                        <!-- Payment Bangla -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Payment Method (Bangla)
                            </label>

                            <input v-model="form.payment_method.bn" type="text" placeholder="৩ কিস্তিতে পেমেন্ট"
                                class="input" />
                        </div>

                        <!-- Discount English -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Discount (English)
                            </label>

                            <input v-model="form.discount.en" type="text" placeholder="20% OFF" class="input" />
                        </div>

                        <!-- Discount Bangla -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Discount (Bangla)
                            </label>

                            <input v-model="form.discount.bn" type="text" placeholder="২০% ছাড়" class="input" />
                        </div>

                        <!-- Duration English -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tour Duration (English)
                            </label>

                            <input v-model="form.tour_duration.en" type="text" placeholder="3 Days / 2 Nights"
                                class="input" />
                        </div>

                        <!-- Duration Bangla -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tour Duration (Bangla)
                            </label>

                            <input v-model="form.tour_duration.bn" type="text" placeholder="৩ দিন / ২ রাত"
                                class="input" />
                        </div>

                        <!-- URL -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                URL
                            </label>

                            <input v-model="form.url" type="text" placeholder="/tour-package/coxs-bazar"
                                class="input" />
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Order
                            </label>

                            <input v-model.number="form.order" type="number" min="0" class="input" />
                        </div>

                        <!-- Image -->
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Image
                            </label>

                            <input type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-gray-300
                   bg-white text-sm text-gray-600
                   file:mr-4 file:border-0 file:bg-gray-100
                   file:px-4 file:py-2.5 file:text-sm
                   file:font-medium" @change="handleImageChange" />

                            <!-- Image Preview -->
                            <div v-if="imagePreview" class="mt-3">
                                <img :src="imagePreview" alt="Preview" class="h-32 w-52 rounded-lg object-cover" />
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label class="inline-flex cursor-pointer items-center gap-3">
                                <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600
                     focus:ring-blue-500" />

                                <span class="text-sm font-medium text-gray-700">
                                    Active
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex flex-col-reverse gap-3 border-t pt-5
               sm:flex-row sm:justify-end">
                        <button type="button" class="rounded-lg border border-gray-300 px-5 py-2.5
                 text-sm font-medium text-gray-700 hover:bg-gray-50" :disabled="saving" @click="closeModal">
                            Cancel
                        </button>

                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm
                 font-semibold text-white transition hover:bg-blue-700
                 disabled:cursor-not-allowed disabled:opacity-60" :disabled="saving">
                            <i v-if="saving" class="bi bi-arrow-repeat mr-1 animate-spin"></i>

                            <i v-else class="bi bi-check-lg mr-1"></i>

                            {{ saving ? "Saving..." : "Save Offer" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
            @click.self="closeDeleteModal">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <!-- Icon -->
                <div class="mx-auto flex h-14 w-14 items-center justify-center
             rounded-full bg-red-100 text-red-600">
                    <i class="bi bi-trash3 text-2xl"></i>
                </div>

                <!-- Content -->
                <div class="mt-4 text-center">
                    <h2 class="text-lg font-bold text-gray-800">
                        Delete Offer Show?
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Are you sure you want to delete
                        <span class="font-semibold text-gray-700">
                            "{{ label(deletingOffer?.title) || "this offer" }}"
                        </span>
                        ?
                    </p>

                    <p class="mt-1 text-xs text-red-500">
                        This action cannot be undone.
                    </p>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex gap-3">
                    <button type="button" class="flex-1 rounded-lg border border-gray-300
               px-4 py-2.5 text-sm font-medium text-gray-700
               transition hover:bg-gray-50" :disabled="deleting" @click="closeDeleteModal">
                        Cancel
                    </button>

                    <button type="button" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5
               text-sm font-semibold text-white transition
               hover:bg-red-700 disabled:cursor-not-allowed
               disabled:opacity-60" :disabled="deleting" @click="confirmDelete">
                        <i v-if="deleting" class="bi bi-arrow-repeat mr-1 animate-spin"></i>

                        <i v-else class="bi bi-trash3 mr-1"></i>

                        {{ deleting ? "Deleting..." : "Delete" }}
                    </button>
                </div>
            </div>
        </div>





        <!-- Toast -->
        <div v-if="toast.show" class="fixed right-5 top-5 z-50 rounded-lg px-5 py-3
             text-sm font-medium text-white shadow-lg" :class="toast.type === 'success'
                    ? 'bg-green-600'
                    : 'bg-red-600'
                ">
            {{ toast.message }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from "vue";
import api from "@/services/api";

interface LocalizedText {
    en?: string | null;
    bn?: string | null;
}

interface OfferShow {
    id: number;
    title: LocalizedText | null;
    description: LocalizedText | null;
    location: LocalizedText | null;
    price: number | string | null;
    payment_method: LocalizedText | null;
    discount: LocalizedText | null;
    tour_duration: LocalizedText | null;
    image: string | null;
    url: string | null;
    order: number | null;
    is_active: boolean;
}

interface OfferForm {
    title: LocalizedText;
    description: LocalizedText;
    location: LocalizedText;
    price: string | number;
    payment_method: LocalizedText;
    discount: LocalizedText;
    tour_duration: LocalizedText;
    image: File | null;
    url: string;
    order: number;
    is_active: boolean;
}

const localizedFields = [
    "title",
    "description",
    "location",
    "payment_method",
    "discount",
    "tour_duration",
] as const;

const emptyForm = (): OfferForm => ({
    title: { en: "", bn: "" },
    description: { en: "", bn: "" },
    location: { en: "", bn: "" },
    price: "",
    payment_method: { en: "", bn: "" },
    discount: { en: "", bn: "" },
    tour_duration: { en: "", bn: "" },
    image: null,
    url: "",
    order: 0,
    is_active: true,
});

/* ---------------- State ---------------- */
const offers = ref<OfferShow[]>([]);
const loading = ref(false);

const search = ref("");
const status = ref("");

const showModal = ref(false);
const editingOffer = ref<OfferShow | null>(null);
const form = ref<OfferForm>(emptyForm());
const imagePreview = ref<string | null>(null);
const saving = ref(false);

const showDeleteModal = ref(false);
const deletingOffer = ref<OfferShow | null>(null);
const deleting = ref(false);

const toast = ref({
    show: false,
    message: "",
    type: "success" as "success" | "error",
});

/* ---------------- Helpers ---------------- */
const label = (value: LocalizedText | null | undefined): string => {
    if (!value) return "";
    return value.en || value.bn || "";
};

let toastTimer: ReturnType<typeof setTimeout>;

const showToast = (message: string, type: "success" | "error") => {
    clearTimeout(toastTimer);
    toast.value = { show: true, message, type };

    toastTimer = setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const revokePreview = () => {
    if (imagePreview.value?.startsWith("blob:")) {
        URL.revokeObjectURL(imagePreview.value);
    }
};

/* ---------------- Fetch ---------------- */
const fetchOffers = async () => {
    loading.value = true;

    try {
        const params: Record<string, string> = {};

        if (search.value.trim()) params.search = search.value.trim();
        if (status.value !== "") params.is_active = status.value;

        const response = await api.get("/admin/offer-show", { params });

        offers.value = response.data.data ?? [];
    } catch (error) {
        console.error("Failed to load offers:", error);
        showToast("Failed to load offer shows.", "error");
    } finally {
        loading.value = false;
    }
};

/* ---------------- Search / filters ---------------- */
let searchTimer: ReturnType<typeof setTimeout>;

const filterOffers = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(fetchOffers, 400);
};

const resetFilters = () => {
    search.value = "";
    status.value = "";
    fetchOffers();
};

/* ---------------- Create / Edit ---------------- */
const resetForm = () => {
    revokePreview();
    form.value = emptyForm();
    imagePreview.value = null;
    editingOffer.value = null;
};

const createOffer = () => {
    resetForm();
    showModal.value = true;
};

const editOffer = (offer: OfferShow) => {
    resetForm();
    editingOffer.value = offer;

    form.value = {
        title: { en: offer.title?.en ?? "", bn: offer.title?.bn ?? "" },
        description: {
            en: offer.description?.en ?? "",
            bn: offer.description?.bn ?? "",
        },
        location: { en: offer.location?.en ?? "", bn: offer.location?.bn ?? "" },
        price: offer.price ?? "",
        payment_method: {
            en: offer.payment_method?.en ?? "",
            bn: offer.payment_method?.bn ?? "",
        },
        discount: { en: offer.discount?.en ?? "", bn: offer.discount?.bn ?? "" },
        tour_duration: {
            en: offer.tour_duration?.en ?? "",
            bn: offer.tour_duration?.bn ?? "",
        },
        image: null,
        url: offer.url ?? "",
        order: offer.order ?? 0,
        is_active: !!offer.is_active,
    };

    imagePreview.value = offer.image;
    showModal.value = true;
};

const closeModal = () => {
    if (saving.value) return;

    showModal.value = false;
    resetForm();
};

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    revokePreview();
    form.value.image = file;
    imagePreview.value = URL.createObjectURL(file);
};

const saveOffer = async () => {
    saving.value = true;

    try {
        const formData = new FormData();

        for (const field of localizedFields) {
            formData.append(`${field}[en]`, form.value[field].en ?? "");
            formData.append(`${field}[bn]`, form.value[field].bn ?? "");
        }

        formData.append("price", String(form.value.price ?? ""));
        formData.append("url", form.value.url ?? "");
        formData.append("order", String(form.value.order ?? 0));
        formData.append("is_active", form.value.is_active ? "1" : "0");

        if (form.value.image) {
            formData.append("image", form.value.image);
        }

        const isEditing = !!editingOffer.value;

        if (isEditing) {
            // Laravel can't parse multipart on a real PUT, so spoof it
            formData.append("_method", "PUT");
        }

        const url = isEditing
            ? `/admin/offer-show/${editingOffer.value!.id}`
            : "/admin/offer-show";

        // No manual Content-Type: axios sets it with the correct boundary
        await api.post(url, formData);

        showToast(
            isEditing
                ? "Offer show updated successfully."
                : "Offer show created successfully.",
            "success"
        );

        // Release the lock BEFORE closing, otherwise closeModal() returns early
        saving.value = false;
        closeModal();

        await fetchOffers();
    } catch (error: any) {
        console.error("Save failed:", error);

        const errors = error?.response?.data?.errors;
        const firstError = errors ? (Object.values(errors)[0] as string[])?.[0] : null;

        showToast(
            firstError || error?.response?.data?.message || "Failed to save offer show.",
            "error"
        );
    } finally {
        saving.value = false;
    }
};

/* ---------------- Delete ---------------- */
const deleteOffer = (offer: OfferShow) => {
    deletingOffer.value = offer;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    if (deleting.value) return;

    showDeleteModal.value = false;
    deletingOffer.value = null;
};

const confirmDelete = async () => {
    if (!deletingOffer.value) return;

    deleting.value = true;

    try {
        await api.delete(`/admin/offer-show/${deletingOffer.value.id}`);

        offers.value = offers.value.filter(
            (item) => item.id !== deletingOffer.value!.id
        );

        showToast("Offer show deleted successfully.", "success");

        deleting.value = false;
        closeDeleteModal();
    } catch (error: any) {
        console.error("Delete failed:", error);
        showToast(
            error?.response?.data?.message || "Failed to delete offer show.",
            "error"
        );
    } finally {
        deleting.value = false;
    }
};

/* ---------------- Lifecycle ---------------- */
onMounted(fetchOffers);

onBeforeUnmount(() => {
    clearTimeout(searchTimer);
    clearTimeout(toastTimer);
    revokePreview();
});
</script>

<style scoped>

.input {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  padding: 0.625rem 0.75rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  outline: none;
  background-color: #fff;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px #3b82f6;
}
</style>
