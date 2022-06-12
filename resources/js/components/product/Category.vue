<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Category List</h3>

                <div class="card-tools">
                  
                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Section</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="category in categories.data" :key="category.id" >

                      <td>{{category.id}}</td>
                      <td class="text-capitalize">{{category.name}}</td>
                      <td>{{category.description}}</td>
                      <td>{{ category.section_id != null && tags[category.section_id] != null ? tags[category.section_id].name : '' }}</td>
                      <td>{{category.created_at}}</td>
                      <td>

                        <a href="#" @click="editModal(category)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteCategory(category.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="categories" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Category</h5>
                    <h5 class="modal-title" v-show="editmode">Update Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateCategory() : createCategory()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input v-model="form.description" type="text" name="description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                            <has-error :form="form" field="description"></has-error>
                        </div>
                        <div class="form-group">
                            <label>section</label>
                            <select class="form-control" v-model="form.section_id">
                              <option 
                                  v-for="(cat,index) in tags" :key="index"
                                  :value="index"
                                  :selected="index == form.section">{{ cat.name }}</option>
                            </select>
                            <has-error :form="form" field="section_id"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {
                editmode: false,
                categories : {},
                form: new Form({
                    id : '',
                    name: '',
                    description: '',
                    section_id: '',
                }),
                tags: {},
            }
        },
        methods: {
            deleteCategory(id) {
                Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {

                      // Send request to the server
                        if (result.value) {
                              this.form.delete('/api/category/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadCategories();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
            },
            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('/api/category?page=' + page).then(({ data }) => (this.categories = data.data));

                  this.$Progress.finish();
            },
            updateCategory(){
                let temp = 0;
                
                this.categories.data.map((value, index) => {
                    if(this.form.name == value.name && this.form.id != value.id){
                        temp = 1;
                    }
                });
                if (temp == 1) {
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: 'Category is already exist! Please try again'
                    });
                } else {
                    this.$Progress.start();
                    this.form.put('/api/category/'+this.form.id)
                    .then((response) => {
                        // success
                        $('#addNew').modal('hide');
                        Toast.fire({
                        icon: 'success',
                        title: response.data.message
                        });
                        this.$Progress.finish();
                            //  Fire.$emit('AfterCreate');

                        this.loadCategories();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
                }
                

            },
            editModal(category){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(category);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            loadTags(){
              axios.get("/api/tag/list").then(response => {
                  this.tags = response.data.data;
              }).catch(() => console.warn('Oh. Something went wrong'));
            },
            correctShowTime(time){
                let array = time.split('T');
                let date = array[0].split('-');
                array[1] = array[1].split('.')[0];
                return `${date[1]}-${date[2]}-${date[0]} ${array[1]}`;
            },
            loadCategories(){
                if(this.$gate.isAdmin()){
                    axios.get("/api/category").then(({ data }) => {
                        this.categories = data.data;
                        this.categories.data.map((value, index) => {
                            this.categories.data[index].created_at = this.correctShowTime(value.created_at);
                        });
                    });
                }
            },
            
            createCategory(){
                let temp = 0;
                this.categories.data.map((value, index) => {
                    if(this.form.name == value.name){
                        temp = 1;
                    }
                });
                if (temp == 1) {
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: 'Category is already exist! Please try again'
                    });
                } else {
                    this.form.post('/api/category')
                    .then((response)=>{
                        $('#addNew').modal('hide');

                        Toast.fire({
                                icon: 'success',
                                title: response.data.message
                        });

                        this.$Progress.finish();
                        this.loadCategories();
                    })
                    .catch(()=>{
                        Toast.fire({
                            icon: 'error',
                            title: 'Some error occured! Please try again'
                        });
                    })
                }
                
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadTags();
            this.loadCategories();
            this.$Progress.finish();
        }
    }
</script>
