<template>
  <v-app>
    <v-app-bar app color="indigo darken-2" dark>
      <v-btn icon @click="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
      <v-toolbar-title>My App</v-toolbar-title>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item @click="selectPage('alunos')">
          <v-list-item-title>Alunos</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-container>
        <v-card v-if="selectedPage === 'alunos'">
          <v-card-title>Pesquisar Alunos
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="openAddAlunoDialog">Adicionar Aluno</v-btn>
          </v-card-title>

          <v-card-text>
            <v-text-field v-model="searchText" label="Pesquise por RA ou CPF do aluno" outlined></v-text-field>
            <v-btn @click="show" color="primary">Pesquisar</v-btn>
          </v-card-text>

          <v-data-table dense v-if="alunos.length > 0" :headers="headers" :items="alunos">
            <template v-slot:item="{ item }">
              <tr>
                <td><v-text-field v-model="item.RA" solo dense flat readonly></v-text-field></td>
                <td><v-text-field v-model="item.nome" solo dense flat readonly></v-text-field></td>
                <td><v-text-field v-model="item.cpf" solo dense flat readonly></v-text-field></td>
                <td><v-text-field v-model="item.email" solo dense flat readonly></v-text-field></td>

                <td>
                  <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                      <v-btn icon v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item @click="editAluno(item)">
                        <v-list-item-icon>
                          <v-icon>mdi-pencil</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>Editar</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteAluno(item)">
                        <v-list-item-icon>
                          <v-icon>mdi-delete</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>Excluir</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
            </template>
          </v-data-table>



          <v-alert v-else :value="true" color="warning" icon="mdi-alert-circle-outline">
            Nenhum resultado encontrado.
          </v-alert>

          <v-dialog v-model="addAlunoDialog" max-width="500px">
            <v-card>
              <v-card-title>
                Adicionar Aluno
              </v-card-title>
              <v-card-text>
                <v-form ref="form">
                  <v-text-field v-model="RA" label="RA" outlined></v-text-field>
                  <v-text-field v-model="nome" label="Nome" outlined></v-text-field>
                  <v-text-field v-model="cpf" label="CPF" outlined></v-text-field>
                  <v-text-field v-model="email" label="Email" outlined></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn color="primary" @click="addAluno">Salvar</v-btn>
                <v-btn @click="cancelAddAluno">Cancelar</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<style lang="scss">  tbody {
    tr:hover {
      background-color: transparent !important;
    }
  }
</style>

<script>
import axios from 'axios';

export default {
  name: 'App',
  data() {
    return {
      drawer: true,
      selectedPage: 'alunos',
      searchText: '',
      alunos: [],
      headers: [
        { text: 'Registro Academico', value: 'RA' },
        { text: 'Nome', value: 'nome' },
        { text: 'CPF', value: 'cpf' },
        { text: 'EMAIL', value: 'email' },
      ],
      RA: '',
      nome: '',
      cpf: '',
      email: '',
      addAlunoDialog: false,

    };
  },

  created() {
    this.selectedPage = 'alunos';
    this.getAlunos();
  },

  methods: {
    selectPage(page) {
      this.selectedPage = 'alunos';
      if (page == 'alunos') {
        this.getAlunos();
      }
    },

    addAluno() {
      const formData = {
        RA: this.RA,
        nome: this.nome,
        cpf: this.cpf,
        email: this.email
      };
      axios.post('http://localhost:8000/api/alunos/store', formData)
        .then(response => {
          console.log(response.data);
          this.addAlunoDialog = false;
          this.$refs.form.reset();
          this.$emit('aluno-adicionado', response.data.data);
        })
        .catch(error => {
          console.error(error);
        });
    },

    cancelAddAluno() {
      this.addAlunoDialog = false;
      this.RA = '';
      this.nome = '';
      this.cpf = '';
      this.email = '';
    },

    show() {
      let params = {};
      if (this.searchText) {
        if (this.searchText.match(/^\d+$/)) {
          if (this.searchText.length >= 11 && this.searchText.length <= 14) {
            // CPF
            params.cpf = this.searchText.replace(/[^0-9]/g, ''); // Remove todos os caracteres que não são números
          } else if (this.searchText.length <= 4) {
            // RA
            params.RA = this.searchText;
          }
        }
      }
      axios.get('http://localhost:8000/api/alunos/show', {
        params: params
      })
        .then(response => {
          this.alunos = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },

    getAlunos() {
      axios.get('http://localhost:8000/api/alunos')
        .then(response => {
          this.alunos = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    openAddAlunoDialog() {
      this.addAlunoDialog = true;
    },

    updateAluno(item) {
      axios.put(`http://localhost:8000/api/alunos/${item.id}`, item)
        .then(response => {
          console.log(response.data);
        })
        .catch(error => {
          console.log(error);
        });
    },

  }
};
</script>