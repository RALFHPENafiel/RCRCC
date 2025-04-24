<?php
class SlugGenerator {
    public static function generate(string $text, string $table, string $column): string {
        // Convert to lowercase and replace non-alphanumeric with dashes
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
        $slug = trim($slug, '-');
        
        // Check if slug exists in database
        global $conn;
        $originalSlug = $slug;
        $counter = 1;
        
        while (self::slugExists($slug, $table, $column)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private static function slugExists(string $slug, string $table, string $column): bool {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() > 0;
    }
}