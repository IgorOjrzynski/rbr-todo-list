import { defineStore } from 'pinia'
import axios from 'axios'

/**
 * Sklep Pinia – zadania + paginacja.
 */
export const useTaskStore = defineStore('tasks', {
  // --------------------------
  // State
  // --------------------------
  state: () => ({
    tasks: [],               // lista zadań
    isLoading: false,        // flaga ładowania
    error: null,             // obiekt/tekst błędu

    // aktywne filtry
    filters: {
      status: null,
      priority: null,
      search: ''
    },

    // meta paginacji
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0
    }
  }),

  // --------------------------
  // Getters
  // --------------------------
  getters: {
    /**
     * Zwraca zadania przefiltrowane lokalnie,
     * aby UI reagował bez dodatkowych zapytań.
     */
    filteredTasks (state) {
      const { status, priority, search } = state.filters
      return state.tasks.filter(t => {
        const sOK = !status || t.status === status
        const pOK = !priority || t.priority === priority
        const fOK = !search || t.title.toLowerCase().includes(search.toLowerCase())
        return sOK && pOK && fOK
      })
    },
    canPrev: s => s.pagination.currentPage > 1,
    canNext: s => s.pagination.currentPage < s.pagination.lastPage
  },

  // --------------------------
  // Actions
  // --------------------------
  actions: {
    async fetchTasks (params = {}) {
      this.isLoading = true
      this.error = null

      try {
        const { data } = await axios.get('/api/tasks', { params })
        this.tasks = data.data
        Object.assign(this.pagination, {
          currentPage: data.meta.current_page,
          lastPage:    data.meta.last_page,
          perPage:     data.meta.per_page,
          total:       data.meta.total
        })
      } catch (e) {
        this.error = e
      } finally {
        this.isLoading = false
      }
    },

    async changePage (page) {
      await this.fetchTasks({
        ...this.filters,
        per_page: this.pagination.perPage,
        page
      })
    },

    async createTask (payload) {
      this.isLoading = true
      this.error = null

      try {
        const { data } = await axios.post('/api/tasks', payload)
        this.tasks.unshift(data.data)
      } catch (e) {
        this.error = e
      } finally {
        this.isLoading = false
      }
    },

    async updateTask (id, payload) {
      this.isLoading = true
      this.error = null

      try {
        const { data } = await axios.put(`/api/tasks/${id}`, payload)
        const idx = this.tasks.findIndex(t => t.id === id)
        if (idx !== -1) this.tasks[idx] = data.data
      } catch (e) {
        this.error = e
      } finally {
        this.isLoading = false
      }
    },

    async deleteTask (id) {
      this.isLoading = true
      this.error = null

      try {
        await axios.delete(`/api/tasks/${id}`)
        this.tasks = this.tasks.filter(t => t.id !== id)
      } catch (e) {
        this.error = e
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Pomocnicza metoda do ustawiania filtrów.
     */
    setFilters (obj) {
      this.filters = { ...this.filters, ...obj }
      this.changePage(1)
    }
  }
}) 