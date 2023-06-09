<script setup>
import { onMounted, ref } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';


const toastr = useToastr();
let products = ref({ 'data': [] });
let categories = ref([]);
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const productIdBeingDeleted = ref(null);
let searchCategory = ref([]);
let searchKeyword = ref([]);
const selectedProducts = ref([]);
const selectAll = ref(false)


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
const getProducts = async (page = 1) => {
    let response = await axios.get(`/api/products?page=${page}`)
        .then(response => {
            products.value = response.data.data;
            selectedProducts, value = [];
            selectAll.value = false;
        });


}

//create a product
const createProduct = (values, { resetForm }) => {
    let response = axios.post("/api/products", values)
        .then(response => {
            getProducts()
            $('#productFormModal').modal('hide');
            if (response.data.success) {
                toastr.success(response.data.message)
            }
            else (
                toastr.error(response.data.message)
            )

            // form.value.resetForm();
            resetForm();

        });
}

//update a product
const updateProduct = (values) => {
    let response = axios.put("/api/products/" + formValues.value.id, values)
        .then((response) => {
            getProducts()
            $('#productFormModal').modal('hide');
            if (response.data.success) {
                toastr.success(response.data.message)
            }
            else (
                toastr.error(response.data.message)
            )
            form.value.resetForm();


        });
};

//show add product form
const addProduct = () => {
    editing.value = false;
    form.value.resetForm();
    $('#productFormModal').modal('show');

};

//show edit product form
const editProduct = (product) => {
    editing.value = true;
    form.value.resetForm();
    formValues.value = {
        id: product.id,
        name: product.name,
        category_id: product.category_id,
        description: product.description,
    }
    $('#productFormModal').modal('show');

};

//submit button condition for create and update
const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateProduct(values, actions);
    } else {
        createProduct(values, actions);
    }
}

//show delete modal
const deleteProduct = (product) => {
    productIdBeingDeleted.value = product.id
    $('#deleteUserModal').modal('show');
};

//delete a product
const destroyProduct = () => {

    let response = axios.delete('/api/products/ ' + productIdBeingDeleted.value)
        .then((response) => {
            getProducts()
            $('#deleteUserModal').modal('hide');
            if (response.data.success) {
                toastr.success(response.data.message)
            }
            else (
                toastr.error(response.data.message)
            )
        });
};

//checkbox function that add and remove id (to be use in bulk delete)
const toggleSelection = (product) => {

    const index = selectedProducts.value.indexOf(product.id);

    if (index === -1) {
        selectedProducts.value.push(product.id)
    }
    else {
        selectedProducts.value.splice(index, 1)
    }
}

//bulk delete a product
const bulkDelete = () => {
    let response = axios.delete('/api/products', {

        data: {
            ids: selectedProducts.value
        }
    }).then(response => {
        getProducts()
        selectedProducts, value = [];
        selectAll.value = false;
        if (response.data.success) {
            toastr.success(response.data.message)
        }
        else (
            toastr.error(response.data.message)
        )
    });
}

//checkbox select all (to be use in bulk delete)
const selectAllProducts = () => {
    if (selectAll.value) {
        selectedProducts.value = products.value.data.map(products => products.id)
    }
    else {
        selectedProducts.value = [];
    }

    console.log(selectedProducts.value)
}

//functions onMount
onMounted(async () => {
    getProducts()
    getCategory()
})





//search
const searchQuery = async () => {
    let response = await axios.get("/api/products/search?s=" + searchKeyword.value + "&c=" + searchCategory.value)
    products.value = response.data.data;
}

//get categories for form category dropdown
const getCategory = () => {
    axios.get("/api/category")
        .then((response) => {
            categories.value = response.data.data;
        })
}



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
            <div class="d-flex">

                <button @click="addProduct()" type="button" class="btn btn-primary mb-2">
                    <i class="fa fa-plus-circle mr-1"></i>
                    Add New
                </button>
                <div v-if="selectedProducts.length > 0">
                    <button @click="bulkDelete()" type="button" class="btn btn-danger mb-2 ml-2">
                        <i class="fa fa-trash mr-1"></i>
                        Delete Selected
                    </button>
                    <span class="ml-2">Selected {{ selectedProducts.length }} products</span>
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-md-3 offset-md-6 mb-2">
                    <div class="input-group">
                        <select class="select2 form-control form-control-md" v-model="searchCategory">
                            <option value=""></option>
                            <option v-for="category in categories" :value="category.id">
                                {{ category.category_name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input v-model="searchKeyword" type="search" class="form-control form-control-md"
                            placeholder="Type your keywords here">
                        <div class="input-group-append">
                            <button @click.prevent="searchQuery" type="submit" class="btn btn-md btn-default">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

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
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllProducts" /></th>
                                <th style="width: 10px">#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="products.data.length > 0">
                            <tr v-for="product in products.data" :key="product.id">
                                <th><input type="checkbox" :checked="selectAll" @change="toggleSelection(product)" /></th>
                                <td>{{ product.id }}</td>
                                <td>{{ product.name }}</td>
                                <td>{{ product.category.category_name }}</td>
                                <td>{{ product.description }}</td>
                                <td>{{ product.created_at }}</td>
                                <td>{{ product.updated_at }}</td>
                                <td><a href="#" @click.prevent="editProduct(product)"><i class="fa fa-edit"></i></a><a
                                        href="#" @click.prevent="deleteProduct(product)"><i
                                            class="fa fa-trash text-danger ml-2"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Bootstrap4Pagination :data="products" @pagination-change-page="getProducts" />
                </div>
            </div>

            <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">
                                <span>Delete Product</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <h5>Are you sure you want to delete this product?</h5>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button @click.prevent="destroyProduct" type="submit" class="btn btn-primary">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
