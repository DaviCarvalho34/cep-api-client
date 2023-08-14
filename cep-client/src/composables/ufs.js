import { ref } from "vue";
import axios, { Axios } from "axios";
import { useRouter } from "vue-router";


export default function useUfs() {
    
    const ufs = ref([]);
    const uf = ref([]);
    const errors = ref({});
    const router = useRouter();

    const getUfs = async () => {
        const response = await axios.get("https://servicodados.ibge.gov.br/api/v1/localidades/estados/");
        ufs.value = response.data;
    };

    return {
        uf,
        ufs,
        getUfs,
    }

}