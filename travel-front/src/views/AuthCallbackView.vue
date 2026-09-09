<template>
    <div class="flex flex-col justify-center items-center min-h-screen bg-gray-50">
        <div class="flex items-center space-x-3">
            <i class="bi bi-arrow-repeat animate-spin text-3xl text-red-600"></i>
            <p class="text-gray-700 text-lg font-medium">login...</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

// Top-Right Success Toast Notification
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    background: '#16a34a',
    color: '#ffffff',
    iconColor: '#ffffff',
});

onMounted(() => {
    const token = route.query.token as string | undefined;
    const userRaw = route.query.user as string | undefined;
    const error = route.query.error as string | undefined;

    // Handle authentication failures or missing parameters
    if (error || !token || !userRaw) {
        Swal.fire({
            icon: 'error',
            title: 'login faild',
            confirmButtonText: 'ok',
            confirmButtonColor: '#4B5563'
        }).then(() => router.push('/login'));
        return;
    }

    try {
        // Expressive parsing: user comes already formatted from route.query
        const user = typeof userRaw === 'string' && userRaw.startsWith('{') 
            ? JSON.parse(userRaw) 
            : JSON.parse(decodeURIComponent(userRaw));

        // Save session state
        localStorage.setItem('apiToken', token);
        localStorage.setItem('user', JSON.stringify(user));

        // Show success notification and push home
        Toast.fire({
            icon: 'success',
            title: 'logged successfull with google'
        });

        setTimeout(() => {
            router.push('/');
        }, 600);

    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something is wrong',
            confirmButtonText: 'ok',
            confirmButtonColor: '#4B5563'
        }).then(() => router.push('/login'));
    }
});
</script>