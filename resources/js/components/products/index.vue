<script setup>
import { onMounted, reactive, ref } from 'vue';

let products = ref({});
let categories = ref({});

let form = reactive({
    'name': '',
    'category_id': '',
    'description': '',
});

// Function to reset the form fields
const resetForm = (formObject) => {
    Object.keys(formObject).forEach((key) => {
        formObject[key] = '';
    });
};


const getProducts = async () => {
    let response = await axios.get("/api/products")
    products.value = response.data.data;
}

const search = async () => {
    let response = await axios.get("/api/products?s=" + searchKeyword + "&c=" + searchCategory)
    products.value = response.data
}

const addProduct = async () => {
    let response = await axios.post("/api/products", form)
        .then(response => {
            resetForm(form);
            getProducts()
            $('#createProductModal').modal('hide');
        });
}


const getCategory = () => {
    axios.get("/api/category")
        .then((response) => {
            // console.log(response, 'response')
            categories.value = response.data.data;
        })


}

onMounted(async () => {
    getProducts()
})

getCategory()

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Products</h1>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <!-- Button trigger modal -->
            <button @click="getCategory" type="button" class="btn btn-primary mb-2" data-toggle="modal"
                data-target="#createProductModal">
                Add New
            </button>

            <!-- Modal -->
            <div class="modal fade" id="createProductModal" data-backdrop="static" tabindex="-1" role="dialog"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Add New</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form autocomplete="off">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input v-model="form.name" type="text" class="form-control " id="name" name="name"
                                        aria-describedby="nameHelp" placeholder="Enter product name">
                                </div>

                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    <select v-model="form.category_id" class="form-control " id="category_id"
                                        aria-describedby="categoryHelp" name="category_id">
                                        <option v-for="category in categories" :value="category.id">
                                            {{ category.category_name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input v-model="form.description" type="text" class="form-control " id="description"
                                        aria-describedby="descriptionHelp" placeholder="Enter description"
                                        name="description">
                                </div>

                            </form>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button @click="addProduct" type="button" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td>{{ product.id }}</td>
                                <td>{{ product.name }}</td>
                                <td>{{ product.category.category_name }}</td>
                                <td>{{ product.description }}</td>
                                <td>{{ product.created_at }}</td>
                                <td>{{ product.updated_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>
