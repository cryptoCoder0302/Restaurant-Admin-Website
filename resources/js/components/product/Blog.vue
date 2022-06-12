<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Blog List</h3>

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
                      <th>Created</th>
                      <th>Image</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="tag in tags.data" :key="tag.id">

                      <td>{{tag.id}}</td>
                      <td class="text-capitalize">{{tag.name}}</td>
                      <td>{{tag.description}}</td>
                      <td>{{tag.created_at}}</td>
                      <td><img v-bind:src="'/blog/' + tag.image" width="100" alt="blog"></td>
                      <td>

                        <a href="#" @click="editModal(tag)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteSection(tag.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="tags" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Create New Blog</h5>
                    <h5 class="modal-title" v-show="editmode">Update Blog</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateTag() : createTag()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Description</label>
                            <input v-model="form.description" type="text" name="description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                            <has-error :form="form" field="description"></has-error>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Image</label>
                            <input  type="file" name="Image" accept=".jpg, .jpeg" 
                                @change="updateImage" 
                            class="form-control" :class="{ 'is-invalid': form.errors.has('image') }">
                            <has-error :form="form" field="photo"></has-error>
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
                tags : {},
                form: new Form({
                    id : '',
                    name: '',
                    description: '',
                    image: '',
                    
                })
            }
        },
        methods: {
        updateImage(event){
            this.form.image = event.target.files[0];

            let data = new FormData();
            data.append('file', this.form.image);
            axios.post("/api/blog/upload", data)
                  .then(response => {
                    this.form.image = response.data.data;
                  }).catch(err => {

                    });
          },
            deleteSection(id) {
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
                              this.form.delete('/api/blog/'+id).then((res)=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadTags();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
            },
            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('/api/blog?page=' + page).then(({ data }) => (this.tags = data.data));

                  this.$Progress.finish();
            },
            updateTag(){
                let temp = 0;
                this.tags.data.map((value, index) => {
                    if(this.form.name == value.name && this.form.id != value.id){
                        temp = 1;
                    }
                });
                if(temp == 1) {
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: 'Blog is already exist.'
                    });
                } else {
                    this.$Progress.start();
                    this.form.put('/api/blog/'+this.form.id)
                    .then((response) => {
                        // success
                        $('#addNew').modal('hide');
                            Toast.fire({
                            icon: 'success',
                            title: response.data.message
                        });
                        this.$Progress.finish();
                            //  Fire.$emit('AfterCreate');

                        this.loadTags();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });

                }
                
            },
            editModal(tag){
                
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(tag);
                
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            correctShowTime(time){
                let array = time.split('T');
                let date = array[0].split('-');
                array[1] = array[1].split('.')[0];
                return `${date[1]}-${date[2]}-${date[0]} ${array[1]}`;
            },
            loadTags(){
                if(this.$gate.isAdmin()){
                    axios.get("/api/blog").then(({ data }) => {
                        this.tags = data.data;
                        this.tags.data.map((value, index) => {
                            this.tags.data[index].created_at = this.correctShowTime(value.created_at);
                        })
                    });
                }
            },
            
            createTag(){
                let temp = 0;
                this.tags.data.map((value, index) => {
                    if(this.form.name == value.name){
                        temp = 1;
                    }
                });
                if(temp == 1){
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: 'Blog is already exist.'
                    });
                } else {
                    this.form.post('/api/blog')
                    .then((response)=>{
                        $('#addNew').modal('hide');

                        Toast.fire({
                                icon: 'success',
                                title: response.data.message
                        });

                        this.$Progress.finish();
                        this.loadTags();

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
            this.$Progress.finish();
        },
        
    }
</script>
