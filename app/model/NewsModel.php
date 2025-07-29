<?php
namespace App\Model;
use PDO; // On importe la Bdd
use App\Database; 


class NewsModel
{
    private PDO $cnx;
    private string $prefix = 'mvc_';
    public function __construct()
    {
        $database = new Database();
        $this->cnx = $database->getConnection();
    }

    public function insertNews( string $title, string $article, string $author): void
    {
        $ins = $this->cnx->prepare("INSERT INTO {$this->prefix}news (title, article, author, date_created, last_modified) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$title, $article, $author, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    }

    public function listNews()
    {
        $req = $this->cnx->query("SELECT * FROM {$this->prefix}news");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectArticle(int $id): array
    {
        $req = $this->cnx->prepare("SELECT * FROM {$this->prefix}news WHERE id = ?");
        $req->execute([$id]);
        $res = $req->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function updateNews(int $id, string $title, string $article, string $author): void
    {
        $upd = $this->cnx->prepare(
            "UPDATE {$this->prefix}news SET title = ?, article = ?, author = ?, last_modified = ? WHERE id = ?"
        );
        $upd->execute([$title, $article, $author, date('Y-m-d H:i:s'), $id]);
    }

    public function deleteNews(int $id): void
    {
        $del = $this->cnx->prepare("DELETE FROM {$this->prefix}news WHERE id = ?");
        $del->execute([$id]);
    }
}
?>
