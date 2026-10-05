import api from '@/services/api';
import type { TourPackageOfferHotel, TourPackageOfferHotelPayload } from '@/types/tourPackageOfferHotel';

export const tourPackageOfferHotelService = {
  // Get list with optional filters
  async getAll(params?: Record<string, any>) {
    const response = await api.get('/admin/tour-hotel', {
      params: { all_locales: 1, ...params },
    });
    return response.data;
  },

  // Get single record
  async getById(id: number) {
    const response = await api.get(`/admin/tour-hotel/${id}`, {
      params: { all_locales: 1 },
    });
    return response.data;
  },

  // Create new hotel
  async create(payload: TourPackageOfferHotelPayload) {
    const response = await api.post('/admin/tour-hotel', payload);
    return response.data;
  },

  // Update hotel
  async update(id: number, payload: TourPackageOfferHotelPayload) {
    const response = await api.put(`/admin/tour-hotel/${id}`, payload);
    return response.data;
  },

  // Delete hotel
  async delete(id: number) {
    const response = await api.delete(`/admin/tour-hotel/${id}`);
    return response.data;
  },
};