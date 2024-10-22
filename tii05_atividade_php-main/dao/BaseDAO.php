<?php
interface BaseDAO {
    public function getById($id);
    public function getAll();
    public function create($aluno);
    public function update($aluno);
    public function delete($id);
}
?>