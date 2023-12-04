<script setup>

import { ref } from 'vue';
import { formatDate } from '../../helper.js';
import { useToastr } from '../../toastr.js';


const emit = defineEmits(['userDeleted','editUser','confirmationModal'])
const toastr = useToastr();

const roles = ref([
    {
        name: 'Admin',
        value: 1
    },
    {
        name: 'User',
        value: 2,
    }
]);

const props =  defineProps({
    user: Object,
    index: Number,
    selectAll : Boolean,

});





const editUser = (user) =>{
    emit('editUser',user);
}

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
    .then(() => {
        toastr.success('Role changed successfully!');
    })
};

const toggleSelection = () =>{
    emit('toggleSelection',props.user)
}
</script>
<template>
    <tr >
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index+1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ formatDate(user.created_at) }}</td>
        <td>
            <select  class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="(user.role === role.name)">{{ role.name }}</option>

            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmationModal', user.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>

</template>
