<?php

namespace App\Models;

use CodeIgniter\Model;

class HeroSlideModel extends Model
{
    protected $table            = 'hero_slides';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'title',
        'subtitle',
        'bg_image_media_id',
        'primary_btn_text',
        'primary_btn_url',
        'primary_btn_new_tab',
        'secondary_btn_text',
        'secondary_btn_url',
        'secondary_btn_new_tab',
        'overlay_opacity',
        'text_alignment',
        'status',
        'sort_order',
        'publish_at',
        'expire_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all active, non-expired, and published slides ordered by sort_order ASC.
     *
     * @return array
     */
    public function getActiveSlides(): array
    {
        $now = date('Y-m-d H:i:s');
        $builder = $this->builder();
        $builder->where('hero_slides.status', 'ACTIVE');
        $builder->where('hero_slides.deleted_at', null);

        // Schedule window filters
        $builder->groupStart()
                ->where('hero_slides.publish_at', null)
                ->orWhere('hero_slides.publish_at <=', $now)
                ->groupEnd();

        $builder->groupStart()
                ->where('hero_slides.expire_at', null)
                ->orWhere('hero_slides.expire_at >=', $now)
                ->groupEnd();

        $builder->orderBy('hero_slides.sort_order', 'ASC');
        $builder->orderBy('hero_slides.id', 'ASC');

        $slides = $builder->get()->getResultArray();

        if (empty($slides)) {
            return [];
        }

        // Attach media image filepath if bg_image_media_id is set
        $db = \Config\Database::connect();
        if ($db->tableExists('media')) {
            $mediaIds = array_filter(array_column($slides, 'bg_image_media_id'));
            if (!empty($mediaIds)) {
                $mediaMap = [];
                $mediaRows = $db->table('media')->whereIn('id', $mediaIds)->get()->getResultArray();
                foreach ($mediaRows as $m) {
                    $mediaMap[$m['id']] = $m['filepath'];
                }
                foreach ($slides as &$slide) {
                    if (!empty($slide['bg_image_media_id']) && isset($mediaMap[$slide['bg_image_media_id']])) {
                        $slide['bg_image_path'] = $mediaMap[$slide['bg_image_media_id']];
                    }
                }
            }
        }

        return $slides;
    }

    /**
     * Helper to generate a new UUID v4.
     */
    public static function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
