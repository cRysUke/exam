<script setup>
import { onMounted, ref } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';

let products = ref({});
let categories = ref({});
const editing = ref(false);
const formValues = ref();
const form = ref(null);

//form validation
const createProductSchema = yup.object({
    name: yup.string().required().max(30),
    category_id: yup.string().required('category is a required field'),
    description: yup.string().required().max(100),
});

//clear input values
const resetForm = (formObject) => {
    Object.keys(formObject).forEach((key) => {
        formObject[key] = '';
    });
};

//get all products
const getProducts = async () => {
    let response = await axios.get("/api/products")
    products.value = response.data.data;
}

//create a product
const createProduct = (values) => {
    let response = axios.post("/api/products", values)
        .then(response => {
            getProducts()
            form.value.resetForm();
            $('#productFormModal').modal('hide');
        });
}

//update a product
const updateProduct = (values) => {
    let response = axios.put("/api/products/" + formValues.value.id, values)
        .then((response) => {
            getProducts()
            $('#productFormModal').modal('hide');
            form.value.resetForm();
        });
};

//show add product form
const addProduct = () => {
    editing.value = false;
    $('#productFormModal').modal('show');
    resetForm(formValues);
};

//show edit product form
const editProduct = (product) => {
    editing.value = true;
    $('#productFormModal').modal('show');
    formValues.value = {
        id: product.id,
        name: product.name,
        category_id: product.category_id,
        description: product.description,
    }
};

//submit button condition for create and update
const handleSubmit = (values) => {
    if (editing.value) {
        updateProduct(values);
    } else {
        createProduct(values);
    }
}

//search
const search = async () => {
    let response = await axios.get("/api/products?s=" + searchKeyword + "&c=" + searchCategory)
    products.value = response.data
}

//get categories for form category dropdown
const getCategory = () => {
    axios.get("/api/category")
        .then((response) => {
            // console.log(response, 'response')
            categories.value = response.data.data;
        })
}

//functions onMount
onMounted(async () => {
    getProducts()
})

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
            <button @click="addProduct(); getCategory()" type="button" class="btn btn-primary mb-2">
                Add New
            </button>

            <!-- Modal -->
            <div class="modal fade" id="productFormModal" data-backdrop="static" tabindex="-1" role="dialog"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">
                                <span v-if="editing">Edit Product</span>
                                <span v-else>Add New Product</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <Form ref="form" @submit="handleSubmit" :validation-schema="createProductSchema" v-slot="{ errors }"
                            :initial-values="formValues">
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
                                <th>Action</th>
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
                                <td><a href="#" @click.prevent="editProduct(product); getCategory()"><i
                                            class="fa fa-edit"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>
