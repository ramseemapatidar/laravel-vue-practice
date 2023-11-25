<script setup>
import axios from 'axios';
import { ref,  onMounted, reactive } from 'vue';

const users = ref([]);

const formData = reactive({
    name:'',
    email: '',
    password:'',
});



const getUsers = () => {

    axios.get('/api/users')
    .then((response) => {
        users.value = response.data;
    })
}

const createUser = () =>{
    axios.post('/api/users', formData)
    .then((response)=> {
        //users.value.push(response.data); // data push in last
        users.value.unshift(response.data); // data push in first
        formData.name = '';
        formData.email = '';
        formData.password = '';
        $('#createUserModal').hide('modal');
        $('.modal-backdrop').remove();
    })

}

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
            <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#createUserModal">Add New User</button>
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Register Date</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user,index) in users" :key="user.id" >
                                <td>{{ index+1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add user Modal -->
    <div class="modal fade" id="createUserModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form autocomplete="off">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input name="name" v-model="formData.name" type="text" class="form-control"
                                id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback"></span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" v-model="formData.email" type="email" class="form-control "
                                 id="email" aria-describedby="nameHelp"
                                placeholder="Enter full name" />
                            <span class="invalid-feedback"></span>
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <input name="password" v-model="formData.password" type="password" class="form-control "
                                 id="password" aria-describedby="nameHelp"
                                placeholder="Enter password" />
                            <span class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" @click="createUser" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End user Modal -->
</template>
