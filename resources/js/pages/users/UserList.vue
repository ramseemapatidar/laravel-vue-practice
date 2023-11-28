<script setup>
import axios from 'axios';
import { ref,  onMounted, watch } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import UserListItem from './UserListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const users = ref({'data': []});
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();
const searchQuery = ref(null);
const selectedUsers = ref([]);
const selectAll = ref(false);

const getUsers = (page = 1) => {

    axios.get(`/api/users?page=${page}`)
    .then((response) => {
        users.value = response.data;
    })
}
const schema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(3),
})

const editschema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(3) : schema;
    }),

})

const createUser = (values ,{ resetForm, setErrors }) =>{
   axios.post('/api/users',values)
   .then((response) => {
        users.value.unshift(response.data);
        $('#userFormModal').modal('hide');
        resetForm();
        toastr.success('User created successfully!');
   })
   .catch((error) => {
        if (error.response.data.errors) {
            setErrors(error.response.data.errors);
        }
    })
}
const addUserModal = () =>{
    editing.value = false;
    $('#userFormModal').modal('show');
}
const editUser = (user)=>{

    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
}

const updateUser = (values, { setErrors }) => {

    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User updated successfully!');

        }).catch((error) => {

            setErrors(error.response.data.errors);

        });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}

const userDeleted = (userId) => {
    users.value = users.value.filter(user => user.id !== userId);
}

const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log(selectedUsers.value);
};

const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value
        }
    })
    .then(response => {
        users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
        selectedUsers.value = [];
        selectAll.value = false;
        toastr.success(response.data.message);
    });
};

const selectAllUsers = () => {
    if (selectAll.value) {
        selectedUsers.value = users.value.data.map(user => user.id);
    } else {
        selectedUsers.value = [];
    }
    console.log(selectedUsers.value);
}

const search = () =>{
    axios.get('/api/users/search',{
        params :{
            query : searchQuery.value
        }
    })
    .then(response=>{
        users.value = response.data;
    })
}

watch(searchQuery, debounce(() => {
    search();
},300));


onMounted(() => {
    getUsers()

});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button type="button" class="btn btn-primary mb-2" @click="addUserModal">Add New User</button>
                    <div v-if="selectedUsers.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedUsers.length }} users</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers" /></th>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Register Date</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length>0">
                            <UserListItem  v-for="(user,index) in users.data"
                            :key="user.id"
                            :user="user"
                            :index="index"
                            @user-deleted="userDeleted"
                            @edit-user="editUser"
                            @toggle-selection="toggleSelection"
                            :select-all="selectAll"></UserListItem>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No result found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-if="users.data.length>0">
                <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" />
            </div>
        </div>
    </div>

    <!-- Add user Modal -->
    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? schema : editschema" v-slot="{errors}" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control " :class="{ 'is-invalid': errors.email }" id="email" aria-describedby="nameHelp"
                                placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <Field name="password" type="password" class="form-control " :class="{ 'is-invalid': errors.password }" id="password" aria-describedby="nameHelp"
                                placeholder="Enter password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ editing ? 'Update' : 'Save' }}</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
    <!-- End user Modal -->


</template>
