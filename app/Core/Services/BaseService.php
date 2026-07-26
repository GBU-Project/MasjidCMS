<?php

namespace App\Core\Services;

use Config\Services;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\ValidationInterface;
use Psr\Log\LoggerInterface;
use App\Core\Exceptions\ValidationException;

/**
 * Class BaseService
 *
 * Parent class untuk seluruh Service layer di MasjidCMS.
 * Menyediakan utilitas umum seperti logging, validasi, dan manajemen transaksi database.
 */
abstract class BaseService
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ValidationInterface
     */
    protected ValidationInterface $validator;

    /**
     * @var BaseConnection|null
     */
    protected ?BaseConnection $db = null;

    public function __construct(?LoggerInterface $logger = null, ?ValidationInterface $validator = null)
    {
        $this->logger = $logger ?? Services::logger();
        $this->validator = $validator ?? Services::validation();
    }

    /**
     * Mendapatkan koneksi database default.
     *
     * @return BaseConnection
     */
    protected function getDb(): BaseConnection
    {
        if ($this->db === null) {
            $this->db = \Config\Database::connect();
        }

        return $this->db;
    }

    /**
     * Helper untuk memvalidasi input data berdasarkan aturan tertentu.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return bool
     * @throws ValidationException
     */
    protected function validate(array $data, array $rules, array $messages = []): bool
    {
        $this->validator->reset();
        $this->validator->setRules($rules, $messages);

        if (!$this->validator->run($data)) {
            throw new ValidationException('Validation failed', $this->validator->getErrors());
        }

        return true;
    }

    /**
     * Wrapper transaksi database aman (Auto-rollback jika exception).
     *
     * @param callable $callback
     * @return mixed
     * @throws \Throwable
     */
    protected function transaction(callable $callback): mixed
    {
        $db = $this->getDb();
        $db->transBegin();

        try {
            $result = $callback($db);

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \RuntimeException('Database transaction failed.');
            }

            $db->transCommit();
            return $result;
        } catch (\Throwable $e) {
            $db->transRollback();
            $this->logger->error('Transaction rolled back: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Utility umum untuk mencatat log info.
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Utility umum untuk mencatat log error.
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
