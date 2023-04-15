<script setup>
import { onMounted, ref } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';

let products = ref({});
let categories = ref({});



const getProducts = async () => {
    let response = await axios.get("/api/products")
    products.value = response.data.data;
}

const search = async () => {
    let response = await axios.get("/api/products?s=" + searchKeyword + "&c=" + searchCategory)
    products.value = response.data
}



const addProduct = (values, { resetForm }) => {
    let response = axios.post("/api/products", values)
        .then(response => {
            getProducts()
            $('#createProductModal').modal('hide');
            resetForm();
        });
}




const schema = yup.object({
    name: yup.string().required().max(30),
    category_id: yup.string().required('category is a required field'),
    description: yup.string().required().max(100),
});

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
                        <Form @submit="addProduct" :validation-schema="schema" v-slot="{ errors }">
                            <div class="modal-body">

                                <div class="form-group">

                                    <label for="name">Name</label>
                                    <Field type="text" class="form-control " :class="{ 'is-invalid': errors.name }"
                                        id="name" name="name" aria-describedby="nameHelp"
                                        placeholder="Enter product name" />
                                    <span class="invalid-feedback">{{ errors.name }}</span>
                                </div>

                                <div class="form-group">

                                    <label for="category_id">Category</label>
                                    <Field as="select" class="form-control " :class="{ 'is-invalid': errors.category_id }"
                                        id="category_id" aria-describedby="categoryHelp" name="category_id">
                                        <option v-for="category in categories" :value="category.id">
                                            {{ category.category_name }}
                                        </option>
                                    </Field>
                                    <span class="invalid-feedback">{{ errors.category_id }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <Field type="text" class="form-control" :class="{ 'is-invalid': errors.description }"
                                        id="description" aria-describedby="descriptionHelp" placeholder="Enter description"
                                        name="description" />
                                    <span class="invalid-feedback">{{ errors.description }}</span>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </Form>

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
