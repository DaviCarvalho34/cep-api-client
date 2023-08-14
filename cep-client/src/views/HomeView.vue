<script setup>

import { reactive } from 'vue';
import useAddresses from '../../src/composables/addresses';
import useUfs from '../composables/ufs';
import { onMounted } from 'vue';
import { ref } from 'vue';
import { watch } from 'vue';



const { addresses, address, getAddresses, storeAddress, destroyAddress, getAddress, updateAddress, searchByCep, addAddressByCep, formSearchCep } = useAddresses();
const { ufs, getUfs } = useUfs();
const selectedAddressId = ref(null);

const form = reactive({
  cep:"",
  road:"",
  neighborhood:"",
  uf:"",
  city:""
});

const updateForm = reactive({
  cep:"",
  road:"",
  neighborhood:"",
  uf:"",
  city:""
});

watch(() => form.cep, async (newCep) => {
  if (newCep.length === 9) { 
    await cepForm(newCep);
  }
});

const searchCepQuery = ref('');

onMounted(async () => {
  await getAddresses();
  await getUfs();
});

const resetForm = () => {
  form.cep = '';
  form.road = '';
  form.neighborhood = '';
  form.uf = '';
  form.city = '';
};

const resetUpdateForm = () => {
  updateForm.cep = '';
  updateForm.road = '';
  updateForm.neighborhood = '';
  updateForm.uf = '';
  updateForm.city = '';
};

const showToast = ref(false);
const showUpdateToast = ref(false);
const showDeleteToast = ref(false);
const showUpdateErrorToast = ref(false);
const showCreateErrorToast = ref(false);

const showUpdateModal = ref(false);

const addressPlaceholderEdit = async (id) => {
    try {
        const selectedAddress = await getAddress(id);
       
        if (selectedAddress) {
          selectedAddressId.value = selectedAddress.id;
          updateForm.cep = selectedAddress.cep || "";
          updateForm.road = selectedAddress.road || "";
          updateForm.neighborhood = selectedAddress.neighborhood || "";
          updateForm.uf = selectedAddress.uf || "";
          updateForm.city = selectedAddress.city || "";
           
        }
    } catch (error) {
        console.error(error);
    }
};

const cepForm = async (cep) => {
  try {
    const result = await formSearchCep(cep);
    if (result) {
      form.city = result.localidade;
      form.uf = result.uf;
      form.road = result.logradouro;
      form.neighborhood = result.bairro;
    }
  } catch(error){
    console.error("Error searching by CEP:", error);
  }
}


const searchCep = async (cep) => {
    try {
        const result = await searchByCep(cep);       
    } catch (error) {
        console.error("Error searching by CEP:", error);
    }
};

const createAddress = async () => {
  try {
    const response = await storeAddress(form);
    if(response.status === 200 || response.status === 201) {
      getAddresses();
      form.cep = '';
      form.road = '';
      form.neighborhood = '';
      form.uf = '';
      form.city = '';
      
      showToast.value = true;
      
      setTimeout(() => {
        showToast.value = false;
        window.location.reload();
      }, 500);
    }   
  } catch (error) {
    showCreateErrorToast.value = true;
    setTimeout(() => {
      showCreateErrorToast.value = false;
      }, 7000);
  }
};

const saveLocalStorage = (data) => {
    const savedAdresses = JSON.parse(localStorage.getItem('savedAdresses')) || [];
    savedAdresses.push(data);
    localStorage.setItem('enderecosSalvos', JSON.stringify(savedAdresses));
};

const editAddress = async () => {
    try {
        const response = await updateAddress(selectedAddressId.value, updateForm);
        if (response.status === 200) {
            saveLocalStorage(updateForm);
            getAddresses();
            updateForm.cep = '';
            updateForm.road = '';
            updateForm.neighborhood = '';
            updateForm.uf = '';
            updateForm.city = '';

            showUpdateModal.value = true;
            showUpdateToast.value = true;
            setTimeout(() => {
                showUpdateToast.value = false;
                window.location.reload();
            }, 500);           
        }
    } catch (error) {
      showUpdateErrorToast.value = true;      
      setTimeout(() => {
          showUpdateErrorToast.value = false
      }, 7000);   
    }
};

const deleteAddress = async (id) => {
  try {
    const confirmed = window.confirm("Tem certeza que deseja apagar o endereço selecionado?");
    if (!confirmed) {
      return;
    }

    await destroyAddress(id);

    showDeleteToast.value = true;
    setTimeout(() => {
      showDeleteToast.value = false;
    }, 7000);
  } catch (error) {
    // Lida com erro, se necessário
  }
};

</script>
<template>

<div id="update-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 w-full hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" >
  <div class="relative w-full max-w-md max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
          <!-- Modal header -->
          <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
              <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                  Atualizar endereço
              </h3>
              <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="update-modal">
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                  </svg>
                  <span class="sr-only">Close modal</span>
              </button>
          </div>

          <form @submit.prevent="editAddress(selectedAddressId)">
            <div class="p-6 space-y-6">
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    
                    <div>
                        <label for="cep" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cep</label>
                        <input v-model="updateForm.cep" type="text" id="cep" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="000000-000" required>
                    </div>  
                    <div>
                        <label for="road" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Logradouro</label>
                        <input v-model="updateForm.road" type="text" id="road" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Rua"  required>
                    </div>
                    <div>
                        <label for="neighborhood" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bairro</label>
                        <input v-model="updateForm.neighborhood" type="text" id="neighborhood" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Bairro" required>
                    </div>
                    <div>
                      <label for="uf" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">UF</label>
                      <select v-model="updateForm.uf" id="uf" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected disabled>UF</option>
                        <option v-for="uf in ufs" :key="uf.id">{{ uf.sigla }}</option>
                      </select>
                  </div>
                    
                </div>
                <div class="mb-6">
                    <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cidade</label>
                    <input v-model="updateForm.city" type="text" id="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cidade" required>
                </div> 
                
               
                
                
              </div>
              <!-- Modal footer -->
              <div class="p-6 flex gap-3">
                <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800">Salvar</button>
                <button @click="resetUpdateForm();" data-modal-hide="update-modal" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Cancelar</button>
              </div>
            </form>      
      </div>
  </div>
</div>


<div class="max-w-7xl mx-auto mt-12 flex flex-auto w-full flex-wrap justify-between gap-5">
  <div class="w-full flex justify-center">
    <form class="w-9/12 md:w-6/12" @submit.prevent="searchCep(searchCepQuery)">   
      <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Buscar</label>
      <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
              </svg>
          </div>
          <input v-model="searchCepQuery" mask-reverse type="search" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pesquise por CEP" required>
          <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm px-4 py-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800">Buscar</button>
      </div>
    </form> 
  </div>

  <div class="max-w-7xl mx-auto flex justify-end">
    <button  data-modal-target="create-modal" data-modal-toggle="create-modal" class="text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Novo Endereço</button>     
  </div>

  <div class="w-full flex flex-col md:flex-row md:flex-wrap items-center gap-3">
    <div v-for="address in addresses" :key="address.id" class="w-4/5 md:w-96 rounded overflow-hidden shadow-lg w-4/12 mt-12">
      <div class="px-6 py-4">
        <h2 class="font-bold text-xl mb-2">{{ address.city }}</h2>
        <h3 class="text-gray-700 text-base">
          {{ address.cep }}
        </h3>
        <p>{{ address.road }}</p>
        <p>{{ address.neighborhood }}</p>
        <p>{{ address.uf }}</p>
      </div>
      <div class="px-6 pt-2 pb-2">
        <form @submit.prevent="addressPlaceholderEdit(address.id)">
          <button data-modal-target="update-modal" data-modal-toggle="update-modal" type="button" class="inline-block bg-green-200 hover:bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
        @click="addressPlaceholderEdit(address.id);">#EDITAR</button>
          <button @click="deleteAddress(address.id)" class="inline-block bg-red-200 hover:bg-red-500 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#DELETAR</button>
        </form>
      </div>
    </div>
  </div>
  
</div>



<div id="create-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 w-full hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
>
  <div class="relative w-full max-w-md max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
          <!-- Modal header -->
          <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
              <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                  Create Address
              </h3>
              <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="create-modal">
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                  </svg>
                  <span class="sr-only">Close modal</span>
              </button>
          </div>
          <!-- Modal body -->
          <form @submit.prevent="createAddress()">
          <div class="p-6 space-y-6">
              <div class="grid gap-6 mb-6 md:grid-cols-2">                      
                  <div>
                      <label for="cep" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cep</label>
                      <input v-model="form.cep" type="text" id="cep" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="000000-000" required>
                  </div>  
                  <div>
                      <label for="road" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Logradouro</label>
                      <input v-model="form.road" type="text" id="road" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Rua"  required>
                  </div>
                  <div>
                      <label for="neighborhood" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bairro</label>
                      <input v-model="form.neighborhood" type="text" id="neighborhood" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Bairro" required>
                  </div>
                  <div>
                    <label for="uf" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">UF</label>
                    <select v-model="form.uf" :disabled="form.uf" id="uf" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                      <option selected>UF</option>
                      <option v-for="uf in ufs" :key="uf.id">{{ uf.sigla }}</option>
                    </select>
                </div>
                  
              </div>
              <div class="mb-6">
                  <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cidade</label>
                  <input v-model="form.city" :disabled="form.city" type="text" id="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cidade" required>
              </div>                   
            </div>
            <!-- Modal footer -->
            <div class="p-6 flex gap-3">
              <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800">Salvar</button>
              <button @click="resetForm();" data-modal-hide="create-modal" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Cancelar</button>
            </div>
          </form>
          
      </div>
  </div>
</div>

<div v-if="showToast" id="toast-success" class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
      <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
      </svg>
      <span class="sr-only">Check icon</span>
  </div>
  <div class="ml-3 text-sm font-normal">Enderço adicionado.</div>  
</div>

<div v-if="showUpdateToast" id="toast-success-update" class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
      <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
      </svg>
      <span class="sr-only">Check icon</span>
  </div>
  <div class="ml-3 text-sm font-normal">Endereço atualizado.</div>
  
</div>

<div v-if="showUpdateErrorToast" id="toast-danger-error" class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
      <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
      </svg>
      <span class="sr-only">Error icon</span>
  </div>
  <div class="ml-3 text-sm font-normal">Erro ao atualizar.</div>
 
</div>

<div v-if="showCreateErrorToast" id="toast-danger-error" class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
    </svg>
    <span class="sr-only">Error icon</span>
</div>
<div class="ml-3 text-sm font-normal">Erro ao cadastrar.</div>

</div>
  

<div v-if="showDeleteToast" id="toast-danger" class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
      <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
      </svg>
      <span class="sr-only">Error icon</span>
  </div>
  <div class="ml-3 text-sm font-normal">Endereço deletado.</div>
  
</div>

  
</template>

