<template>
  <v-app>
    <v-app-bar app color="indigo darken-2" dark>
      <v-btn icon @click="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
      <v-toolbar-title>Menu</v-toolbar-title>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item @click="selectPage('Students')">
          <v-list-item-title>Alunos</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-container>
        <v-card v-if="selectedPage === 'Students'">
          <v-card-title>Pesquisar Students
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="AddStudentDialog">Adicionar Aluno</v-btn>
          </v-card-title>

          <v-card-text>
            <v-text-field v-model="searchText" label="Pesquise por RA ou CPF do aluno" outlined></v-text-field>
            <v-btn @click="show" color="primary">Pesquisar</v-btn>
          </v-card-text>

          <v-data-table dense v-if="Students.length > 0" :headers="headers" :items="Students">
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
                      <v-list-item @click="selectEditStudent(item)">
                        <v-list-item-icon>
                          <v-icon>mdi-pencil</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>Editar</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteStudent(item.RA)">
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

          <v-dialog v-model="addStudentDialog" max-width="500px">
            <v-card>
              <v-card-title>
                Adicionar Aluno
              </v-card-title>
              <v-card-text>
                <v-form ref="form">
                  <v-text-field v-model="RA" label="RA * " outlined :rules="[requiredRule]" required></v-text-field>
                  <v-text-field v-model="nome" label="Nome * " outlined :rules="[requiredRule]" required></v-text-field>
                  <v-text-field v-model="cpf" label="CPF * " outlined :rules="[requiredRule]" required></v-text-field>
                  <v-text-field v-model="email" label="Email * " outlined :rules="[requiredRule, emailRule]"
                    required></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn color="primary" :disabled="!formIsValid" @click="addStudent">Salvar</v-btn>
                <v-btn @click="cancelAddStudent">Cancelar</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="editStudentDialog" max-width="600px">
            <v-card>
              <v-card-title>
                Editar Aluno
              </v-card-title>
              <v-card-text>
                <v-form>
                  <v-text-field v-model="selectedStudent.RA" label="RA" readonly outlined></v-text-field>
                  <v-text-field v-model="selectedStudent.nome" label="Nome" outlined></v-text-field>
                  <v-text-field v-model="selectedStudent.cpf" label="CPF" outlined></v-text-field>
                  <v-text-field v-model="selectedStudent.email" label="Email" outlined></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn color="primary" @click="saveStudentEdit">Salvar</v-btn>
                <v-btn @click="cancelStudentEdit">Cancelar</v-btn>
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
      selectedPage: 'Students',
      searchText: '',
      Students: [],
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

      addStudentDialog: false,
      editStudentDialog: false,

      selectedStudent: {},
      studentEditing: {},
      aluno: {},

      showAlert: false,
      alertMessage: "",
      requiredRule: (value) => !!value || "Campo obrigatório.",
      emailRule: (value) => {
        const emailRegex = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;
        return emailRegex.test(value) || "E-mail inválido.";
      }
    };
  },

  computed: {
    formIsValid() {
      return (
        !!this.RA &&
        !!this.nome &&
        !!this.cpf &&
        !!this.email &&
        this.emailRule(this.email) === true
      );
    }
  },

  created() {
    this.selectedPage = 'Students';
    this.getStudents();
  },

  methods: {
    selectPage(page) {
      this.selectedPage = 'Students';
      if (page == 'Students') {
        this.getStudents();
      }
    },

    addStudent() {
      const formData = {
        RA: this.RA,
        nome: this.nome,
        cpf: this.cpf,
        email: this.email
      };
      axios.post('http://localhost:8000/api/students/store', formData)
        .then(response => {
          console.log(response.data);
          this.addStudentDialog = false;
          this.$refs.form.reset();
          this.$emit('aluno-adicionado', response.data.data);
          this.getStudents();
        })
        .catch(error => {
          console.error(error);
        });

    },

    cancelAddStudent() {
      this.RA = "";
      this.nome = "";
      this.cpf = "";
      this.email = "";
      this.showAlert = false;
      this.alertMessage = "";
      this.addStudentDialog = false;
    },

    cancelStudentEdit() {
      this.addStudentDialog = false;
      this.RA = '';
      this.nome = '';
      this.cpf = '';
      this.email = '';
    },

    selectEditStudent(aluno) {
      // Salva as informações do aluno antes de editar
      this.studentBeforeEdit = {
        RA: aluno.RA,
        nome: aluno.nome,
        cpf: aluno.cpf,
        email: aluno.email
      };
      this.selectedStudent = aluno;
      this.editStudentDialog = true;
    },

    saveStudentEdit() {
      const formData = new FormData();
      const mainStudent = { ...this.selectedStudent };
      const editedStudent = {};
      const editData = {};

      // Copia as informações do aluno editado para a variável studentEditing
      this.studentEditing = { ...this.selectedStudent };

      // Atualiza as informações do aluno editado com as informações preenchidas no formulário
      for (const propriedade in this.studentEditing) {
        if (this.studentEditing[propriedade] !== this.studentBeforeEdit[propriedade]) {
          formData.append(propriedade, this.studentEditing[propriedade]);
          editedStudent[propriedade] = this.studentEditing[propriedade];
          editData[propriedade] = `${propriedade}: ${this.studentEditing[propriedade]}`;
        }
      }

      // Verifica se houve alteração no aluno
      const editTrue = Object.keys(editedStudent).length > 0;

      // Se houver alteração, envia a solicitação PUT para atualizar o aluno
      if (editTrue) {
        axios.put(`http://localhost:8000/api/students/${mainStudent.RA}`, editedStudent)
          .then(response => {
            console.log(response.data);
            console.log(`Informações do aluno antes da edição: ${JSON.stringify(mainStudent)}`);
            console.log(`Informações do aluno após a edição: ${JSON.stringify(this.studentEditing)}`);
            console.log(`Alterações feitas no aluno: ${JSON.stringify(editData)}`);
            this.editStudentDialog = false;
          })
          .catch(error => {
            console.error(error);
          });
      } else {
        console.log('Nenhuma alteração foi feita no aluno.');
        this.editStudentDialog = false;
      }
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
      axios.get('http://localhost:8000/api/students/show', {
        params: params
      })
        .then(response => {
          this.Students = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },

    getStudents() {
      axios.get('http://localhost:8000/api/students')
        .then(response => {
          this.Students = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },


    deleteStudent(RA) {
      const confirmar = confirm(`Tem certeza que deseja excluir o aluno de RA ${RA}?`);
      if (confirmar) {
        axios.delete(`http://localhost:8000/api/students/${RA}`)
          .then(response => {
            console.log(response.data);
            const index = this.Students.findIndex(aluno => aluno.RA === RA);
            if (index !== -1) {
              this.Students.splice(index, 1);
            }
          })
          .catch(error => {
            console.error(error);
            if (error.response.status === 404) {
              alert(`O aluno de RA ${RA} não foi encontrado na base de dados.`);
            } else {
              alert(`Ocorreu um erro ao excluir o aluno de RA ${RA}.`);
            }
          });
      }
    },

    AddStudentDialog() {
      this.addStudentDialog = true;
    },
    openStudentlunoDialog() {
      this.editStudentDialog = true;
    }
  }
};
</script>