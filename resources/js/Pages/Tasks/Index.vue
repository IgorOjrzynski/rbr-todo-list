<template>
  <div class="container py-4">
    <!-- Pasek filtrów -->
    <form class="row g-2 mb-4" @submit.prevent="applyFilters">
      <div class="col-md-3">
        <input
          v-model="localFilters.search"
          type="search"
          class="form-control"
          placeholder="Szukaj po tytule"
        />
      </div>

      <div class="col-md-2">
        <select v-model="localFilters.status" class="form-select">
          <option value="">Status (wszystkie)</option>
          <option value="pending">Oczekuje</option>
          <option value="in-progress">W trakcie</option>
          <option value="completed">Zakończone</option>
        </select>
      </div>

      <div class="col-md-2">
        <select v-model="localFilters.priority" class="form-select">
          <option value="">Priorytet (wszystkie)</option>
          <option value="low">Niski</option>
          <option value="medium">Średni</option>
          <option value="high">Wysoki</option>
        </select>
      </div>

      <div class="col-md-auto">
        <button class="btn btn-primary">
          <i class="bi bi-search"></i> Filtruj
        </button>
      </div>

      <div class="col-md-auto ms-auto">
        <button
          class="btn btn-success"
          @click.prevent="openModal('create')"
        >
          <i class="bi bi-plus-lg"></i> Dodaj zadanie
        </button>
      </div>
    </form>

    <!-- Loader / Error -->
    <div v-if="store.isLoading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>
    <div v-else-if="store.error" class="alert alert-danger">
      {{ store.error.message ?? 'Wystąpił błąd' }}
    </div>

    <!-- Lista zadań -->
    <TaskList
      v-if="!store.isLoading"
      :tasks="store.filteredTasks"
      @edit-task="openModal('edit', $event)"
      @delete-task="deleteTask"
    />

    <!-- Modal z formularzem -->
    <div
      class="modal fade"
      id="taskModal"
      tabindex="-1"
      aria-labelledby="taskModalLabel"
      aria-hidden="true"
      ref="modalEl"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="taskModalLabel">
              {{ modalMode === 'create' ? 'Nowe zadanie' : 'Edycja zadania' }}
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            />
          </div>

          <TaskForm
            :task="selectedTask"
            @submit="submitForm"
            :disabled="store.isLoading"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import TaskList from '@/Components/TaskList.vue'
import TaskForm from '@/Components/TaskForm.vue'
import * as bootstrap from 'bootstrap'

const store = useTaskStore()

// lokalne kopie filtrów (aby nie triggerować od razu fetchTasks)
const localFilters = reactive({ ...store.filters })

// modal
const modalEl = ref(null)
let modalInstance = null
const modalMode = ref('create') // create | edit
const selectedTask = ref(null)

onMounted(async () => {
  modalInstance = new bootstrap.Modal(modalEl.value)
  await store.fetchTasks()
})

function applyFilters () {
  store.setFilters(localFilters)
}

function openModal (mode, task = null) {
  modalMode.value = mode
  selectedTask.value = task
  modalInstance.show()
}

async function submitForm (payload) {
  if (modalMode.value === 'create') {
    await store.createTask(payload)
  } else {
    await store.updateTask(selectedTask.value.id, payload)
  }
  modalInstance.hide()
}

async function deleteTask (task) {
  if (confirm(`Usunąć zadanie "${task.title}"?`)) {
    await store.deleteTask(task.id)
  }
}
</script> 