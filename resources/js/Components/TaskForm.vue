<template>
  <form @submit.prevent="handleSubmit">
    <div class="modal-body">
      <div class="mb-3">
        <label class="form-label">Tytuł</label>
        <input
          v-model="form.title"
          type="text"
          class="form-control"
          required
        />
      </div>

      <div class="mb-3">
        <label class="form-label">Opis</label>
        <textarea
          v-model="form.description"
          class="form-control"
          rows="3"
        ></textarea>
      </div>

      <div class="row g-2">
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select v-model="form.status" class="form-select" required>
            <option disabled value="">Wybierz...</option>
            <option value="pending">Oczekuje</option>
            <option value="in-progress">W trakcie</option>
            <option value="completed">Zakończone</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Priorytet</label>
          <select v-model="form.priority" class="form-select" required>
            <option disabled value="">Wybierz...</option>
            <option value="low">Niski</option>
            <option value="medium">Średni</option>
            <option value="high">Wysoki</option>
          </select>
        </div>
      </div>

      <div class="mt-3">
        <label class="form-label">Termin</label>
        <input
          v-model="form.due_date"
          type="date"
          class="form-control"
        />
      </div>

      <!-- checkbox Google Calendar -->
      <div class="form-check mt-3">
        <input
          v-model="form.sync_calendar"
          class="form-check-input"
          type="checkbox"
          id="syncCalendarSwitch"
        />
        <label class="form-check-label" for="syncCalendarSwitch">
          Dodaj / aktualizuj w Google Calendar
        </label>
      </div>
    </div>

    <div class="modal-footer">
      <button
        type="button"
        class="btn btn-secondary"
        data-bs-dismiss="modal"
      >
        Anuluj
      </button>
      <button type="submit" class="btn btn-primary" :disabled="disabled">
        Zapisz
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  task: {
    type: Object,
    default: null
  },
  disabled: Boolean
})

const emit = defineEmits(['submit'])

/**
 * Lokalna kopia pól formularza.
 */
const form = reactive({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  due_date: '',
  sync_calendar: false
})

watch(
  () => props.task,
  (t) => {
    if (t) {
      Object.assign(form, { ...t, sync_calendar: !!t.sync_calendar })
    } else {
      Object.assign(form, {
        title: '',
        description: '',
        status: 'pending',
        priority: 'medium',
        due_date: '',
        sync_calendar: false
      })
    }
  },
  { immediate: true }
)

function handleSubmit () {
  emit('submit', { ...form })
}
</script> 