<template>
    <div>
        {{ table }}
    </div>
</template>
<script>
export default {
    data() {
        return {
            table: []
        }
    },
    methods: {
    },
    mounted() {
        axios.get(url_base+`/cantidad-por-linea`)
        .then(response => {
            this.table=response.data;
        });
    },
}
</script>