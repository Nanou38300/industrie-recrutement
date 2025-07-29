<?php
Namespace App\View;
class NewsView
{
    public function displayNews(array $news)
    {
        echo "<h1>Liste des news</h1><ul>";
        if (!empty($news)) {
            foreach ($news as $row) {
                $id = (int) $row['id'];
                $title = htmlspecialchars($row['title']);
                $resume = mb_substr($row['article'], 0, 100, 'UTF-8');
                $author = htmlspecialchars($row['author']);
                $date = htmlspecialchars($row['date_created']);

                echo '<blockquote class="liste-news">';
                echo "<h2>{$title}</h2>";
                echo "<header>{$author},  <time datetime=\"{$date}\">{$date}</time></header>";
                echo "<p>{$resume}</p>";
                if($_SESSION){
                    echo "<a class=\"btn blue\" href=\"news/edit/{$id}\">Modifier</a> ";
                    echo "<a class=\"btn red\" href=\"news/delete/{$id}\">Supprimer</a>";
                }
                echo "</blockquote>";
            }
        } else {
            echo 'Pas de données.';
        }
        echo '</ul>';
    }

    public function displayUpdateForm(array $article)
    {
        echo '<h1>Modifier une news</h1>
        <form action="news/edit" method="post">
            <input type="hidden" name="id" value="' . htmlspecialchars($article['id'] ?? '') . '">
            
            <label>Titre</label>
            <input type="text" name="title" value="' . htmlspecialchars($article['title'] ?? '') . '">
            
            <label>Auteur</label>
            <input type="text" name="author" value="' . htmlspecialchars($article['author'] ?? '') . '">
            
            <label>Article</label>
            <textarea name="article">' . htmlspecialchars($article['article'] ?? '') . '</textarea>
            
            <button type="submit">Modifier</button>
        </form>';
    }

    public function displayInsertForm()
    {
        echo '<h1>Insérer un article</h1>
        <form action="?action=news&step=create" method="post">
            
            <label>Titre</label>
            <input type="text" name="title" required >
            
            <label>Auteur</label>
            <input type="text" name="author" required >
            
            <label>Article</label>
            <textarea name="article" required ></textarea>
            
            <button type="submit">Envoyer</button>
        </form>';
    }
}
?>
