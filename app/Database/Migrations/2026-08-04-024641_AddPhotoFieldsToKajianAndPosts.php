<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TASK-031: allow admins to attach a real photo instead of relying on the
 * fixed emoji/gradient placeholder shown on the public homepage for:
 *   - kajian.speaker_photo_media_id : ustadz/penceramah photo (kajian_section.php)
 *   - posts.featured_media_id       : news/berita thumbnail (berita_section.php)
 *
 * Both are nullable so existing rows keep working unchanged and fall back
 * to the current placeholder when no photo has been chosen.
 */
class AddPhotoFieldsToKajianAndPosts extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('kajian') && !$this->db->fieldExists('speaker_photo_media_id', 'kajian')) {
            $this->forge->addColumn('kajian', [
                'speaker_photo_media_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
            ]);
        }

        if ($this->db->tableExists('posts') && !$this->db->fieldExists('featured_media_id', 'posts')) {
            $this->forge->addColumn('posts', [
                'featured_media_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('kajian') && $this->db->fieldExists('speaker_photo_media_id', 'kajian')) {
            $this->forge->dropColumn('kajian', 'speaker_photo_media_id');
        }

        if ($this->db->tableExists('posts') && $this->db->fieldExists('featured_media_id', 'posts')) {
            $this->forge->dropColumn('posts', 'featured_media_id');
        }
    }
}
