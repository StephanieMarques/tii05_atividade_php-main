<?php
require_once 'BaseDAO.php';
require_once 'entity/Aluno.php';
require_once 'entity/Disciplina.php';
require_once 'config/Database.php';

class AlunoDAO
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM Aluno WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Aluno($row['matricula'], $row['nome']);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM Aluno";
        $stmt = $this->db->query($sql);
        $alunos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $alunos[] = new Aluno($row['matricula'], $row['nome']);
        }
        return $alunos;
    }

    public function create($aluno)
    {
        $sql = "INSERT INTO Aluno (nome) VALUES (:nome)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $aluno->getNome());
        $stmt->execute();
    }

    public function update($aluno)
    {
        $sql = "UPDATE Aluno SET nome = :nome WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $aluno->getNome());
        $stmt->bindParam(':id', $aluno->getId());
        $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Aluno WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Método para obter aluno com suas disciplinas
    public function getAlunoWithDisciplinas($alunoID)
    {
        // Preparar a consulta SQL
        $sql = "SELECT a.*, d.* 
                FROM aluno a
                LEFT JOIN disciplina_aluno ad ON a.matricula = ad.aluno_matricula
                LEFT JOIN disciplina d ON ad.disciplina_id = d.id
                WHERE a.matricula = :matricula"; // Usando parâmetro nomeado

        // Preparar a declaração
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':matricula' => $alunoID]); // Executar a consulta com o ID do aluno

        // Inicializando variáveis para aluno e disciplinas
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $disciplina = [];

        // Criar um objeto Aluno a partir do resultado
        if ($row) {
            $aluno = new Aluno($row['matricula'], $row['nome']);
            // Recolher disciplinas
            do {
                $disciplina[] = new Disciplina($row['disciplina_id'], $row['disciplina_nome']); // Supondo que Disciplina tem um construtor apropriado
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
        }

        // Retornar o aluno e suas disciplinas
        return [
            'aluno' => $aluno,
            'disciplina' => $disciplina,
        ];
    }

    }