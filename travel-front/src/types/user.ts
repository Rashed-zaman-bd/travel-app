export interface AdminUser {
  id: number
  name: string
  email: string
  phone: string | null
  role: 'super_admin' | 'admin' | 'user'
  avatar: string | null
  email_verified: boolean
  created_at: string
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}