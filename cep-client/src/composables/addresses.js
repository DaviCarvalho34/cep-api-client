import { ref } from "vue";
import axios, { Axios } from "axios";
import { useRouter } from "vue-router";

axios.defaults.baseURL = "http://127.0.0.1:8000/api/v1/";

export default function useAddresses() {
    
    const addresses = ref([]);
    const address = ref([]);
    const errors = ref({});

    const getAddresses = async () => {
        const response = await axios.get("addresses");
        addresses.value = response.data;
    };

    const getAddress = async (id) => {
        try {
            const response = await axios.get(`addresses/${id}`);
            address.value = response.data.data;
            return response.data.data;
        } catch (error) {
            throw new Error(error);
        }
    };
    
    const searchByCep = async (cep) => {
        try {
            const response = await axios.get(`addresses/search/${cep}`);

            addresses.value = response.data.data;
        } catch (error) {   
            addAddressByCep(cep);          
        }
    };

    const formSearchCep = async (cep) => {
        try {
            const response = await axios.get(`https://viacep.com.br/ws/${cep}/json/`);
            return response.data;
        } catch (error) {
                    
        }
    }

    const storeAddress = async (data) => {
        try {
            const response = await axios.post('addresses', data);
            return response;          
        } catch (error) {
            if(error.response.status === 422) {
                errors.value = error.response.data.errors
            }
        }
    }

    const addAddressByCep = async (cep) => {
        try {
            const response = await axios.get(`https://viacep.com.br/ws/${cep}/json/`);
            
            const { uf, localidade: city, logradouro: road, bairro: neighborhood } = response.data;

            const newAddress = {
                cep: cep,
                uf: uf,
                city: city,
                road: road,
                neighborhood: neighborhood
            };

            await storeAddress(newAddress);
            getAddresses();
        } catch (error) {
            console.error("Erro ao adicionar endereço pelo CEP:", error);
        }
    };

    const updateAddress = async (id,data) => {
        try {
            const response = await axios.put(`addresses/${id}`, data);
            return response;
            
        } catch (error) {
            errors.value = error.response.data.errors;
        }
    }

    const destroyAddress = async (id) => {
        try {
          await axios.delete(`addresses/${id}`);
          await getAddresses();
          
        } catch (error) {
        
        }
      };

    return {
        address,
        addresses,
        getAddress,
        getAddresses,
        storeAddress,
        errors,
        destroyAddress,
        updateAddress,
        searchByCep,
        addAddressByCep,
        formSearchCep
    }

}