<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Order List</h3>

                
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>S.N.</th>
                      <th>Order No.</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Quantity</th>
                      <th>Charge</th>
                      <th>Total Amount</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="tag in tags.data" :key="tag.id">
                      <td>{{tag.id}}</td>
                      <td>{{tag.order_number}}</td>
                      <td>{{`${tag.first_name} ${tag.last_name}`}}</td>
                      <td>{{tag.email}}</td>
                      <td>{{tag.quantity}}</td>
                      <td>{{tag.post_code}}</td>
                      <td>$ {{tag.total_amount}}</td>
                      <td>{{tag.status}}</td>
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
    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {
                editmode: false,
                tags : {},
                
            }
        },
        methods: {
            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('/api/order?page=' + page).then(({ data }) => {
                      this.tags = data.data;
                      console.log(this.tags.data);
                    });

                  this.$Progress.finish();
            },
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },
        
    }
</script>
