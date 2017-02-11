<?php
/* ---------------------------------------------------------
 * src/be/models/managers/UserManager.php
 *
 * A user manager.
 *
 * Copyright 2015 - PROJECT
 * --------------------------------------------------------- */

namespace PROJECT\Models\Managers;

use PROJECT\Exceptions\BadRequestException;
use PROJECT\Exceptions\UnauthorizedException;
use PROJECT\Helpers\UserHelper;
use PROJECT\Helpers\DateHelper;
use Silex\Component\Security\Core\Encoder\JWTEncoder;

/**
 * A user manager.
 * */
class UserManager extends BaseManager
{
    /**
     * Constructor.
     *
     * @param Repository $repo A repository
     * @param Object $cache A cache
     * @param ModelManagerFactory $managerFactory A manager factory
     * */
    public function __construct($repo, $cache, $managerFactory, $filters = array())
    {
        $this->table = 'user';
        $this->table_prefix = 'u';
        $this->entity = 'PROJECT\Models\Entities\User';

        parent::__construct($repo, $cache, $managerFactory, $filters);
    }

    /**
     * Login a user
     *
     * @param $email user email
     * @param $password password email
     * */
    public function login($email, $password)
    {
        $query = $this->repo->getQueryBuilder();

        $query->select(
            'id',
            'firstName',
            'lastName'
        );
        $query->from($this->table, $this->table_prefix);
        $andClause = $query->expr()->andx();
        $andClause->add($query->expr()->eq($this->table_prefix . '.' . 'email', $query->expr()->literal($email)));
        $andClause->add($query->expr()->eq($this->table_prefix . '.' . 'password', $query->expr()->literal(UserHelper::encryptPassword($password))));

        $query->where($andClause);

        $result = $this->repo->get($query);

        if (empty($result)) {
            throw new UnauthorizedException("Email or password incorrect");
        }

        $jwtEncoder = new JWTEncoder(
            $this->app['security.jwt']['secret_key'],
            $this->app['security.jwt']['life_time'],
            $this->app['security.jwt']['algorithm']
        );


        $token = $jwtEncoder->encode([
            'username' => 'admin',
            'email' => $email,
            'firstName' => $result['firstName'],
            'lastName' => $result['lastName'],
        ]);

        return $token;
    }

    /**
     * Login a user
     *
     * @param $email user email
     * */
    public function resetpassword($passwordToken, $newPassword)
    {
        $query = $this->repo->getQueryBuilder();

        $query->select(
            'id'
        );
        $query->from($this->table, $this->table_prefix);
        $andClause = $query->expr()->andx();
        $andClause->add($query->expr()->eq($this->table_prefix . '.' . 'password_token', $query->expr()->literal($passwordToken)));

        $query->where($andClause);

        $result = $this->repo->get($query);

        if (empty($result)) {
            throw new BadRequestException("Token invalid");
        }

        $user = $this->getById($result['id']);
        $user->password = $newPassword;
        $user->password_token = null;
        $user = $this->persist($user);
        return $newPassword;
    }


    /**
     * Login a user
     *
     * @param $email user email
     * */
    public function sendResetpasswordEmail($email)
    {
        $query = $this->repo->getQueryBuilder();

        $query->select(
            'id'
        );
        $query->from($this->table, $this->table_prefix);
        $andClause = $query->expr()->andx();
        $andClause->add($query->expr()->eq($this->table_prefix . '.' . 'email', $query->expr()->literal($email)));

        $query->where($andClause);

        $result = $this->repo->get($query);

        if (empty($result)) {
            throw new BadRequestException("Email not found");
        }

        $passwordToken = UserHelper::generateRandPassword();
        $user = $this->getById($result['id']);
        $user->password_token = $passwordToken;
        $user = $this->persist($user);

        // Method to send email
        return $passwordToken;
    }

    /**
     * Get list of words for a user
     *
     * @param $userId
     * @return mixed
     */
    public function getWordList($userId, $page, $perPage)
    {
        $query = $this->repo->getQueryBuilder();

        $query->select(
            'w.name as name',
            'uw.created_date as created_date'
        );
        $query->from('word', 'w');
        $query->innerJoin('w', 'user_word', 'uw', 'w.id = uw.word_id');
        $query->where($query->expr()->eq('uw.user_id', $userId));
        $query->orderBy('created_date', 'DESC');

        if (!empty($perPage) && !empty($page)) {
            $query
                ->setMaxResults($perPage)
                ->setFirstResult($page * $perPage);
        }

        $result = $this->repo->getList($query);

        return $result;
    }

    public function getTimer($userId, $timeBeforeNewWord = 4 * 60 * 60)
    {
        $lastWord = $this->getWordList($userId, 1, 1);

        $createdDate = null;
        $remainingTimeSeconds = null;

        if (!empty($lastWord) && count($lastWord) > 0) {
            $lastWord = reset($lastWord);
            $createdDate = $lastWord['created_date'];

            $endDate = DateHelper::addSecondToDate($lastWord['created_date'], $timeBeforeNewWord);

            $remainingTimeSeconds = DateHelper::getDiffDate(
                DateHelper::getNow(),
                $endDate
            );

            if ($endDate <= DateHelper::getNow()) {
                $remainingTimeSeconds = 0;
            }
        }

        return [
            'created_date' => $createdDate,
            'remaining_time_seconds' => $remainingTimeSeconds
        ];
    }

    /**
     * Get a word by id for a user
     *
     * @param $userId
     * @return mixed
     */
    public function saveWord($userId, $wordName)
    {
        //todo: create a word manager
        $query = $this->repo->getQueryBuilder();

        $wordManager = new WordManager($this->repo, $this->cache, $this->managerFactory);
        $word = $wordManager->getOne([
            'name' => $wordName
        ]);

        // Find or create the word
        if (empty($word)) {
            $word = $wordManager->create([
                'name' => $wordName,
                'created_date' => date('Y-m-d H:i:s')
            ]);
            $wordId = $wordManager->persist($word);
        } else {
            $wordId = $word->id;
        }

        // Save the word for a user
        $query->insert('user_word');
        $query->setValue('word_id', $wordId);
        $query->setValue('user_id', $userId);
        $query->setValue('created_date', $query->expr()->literal(date('Y-m-d H:i:s')));
        $userWordId = $this->repo->create($query);

        $result = [];
        $result['userWordId'] = $userWordId;
        $result['wordId'] = $wordId;

        return $result;
    }

    /**
     * delete a user
     *
     * @param $userId
     * @return mixed
     */
    public function delete($userId)
    {
        $user = $this->getById($userId);
        $user = parent::delete($user);

        $query = $this->repo->getQueryBuilder();

        $query->select(
            'w.id as wordId'
        );
        $query->from('word', 'w');
        $query->where($query->expr()->eq('w.name', $query->expr()->literal($wordName)));

        $result = $this->repo->get($query);

        // Find or create the word
        if (empty($result)) {
            // create word
            $query->insert('word');
            $query->setValue('name', $query->expr()->literal($wordName));
            $query->setValue('created_date', $query->expr()->literal(date('Y-m-d H:i:s')));
            $wordId = $this->repo->create($query);
        } else {
            $wordId = $result['wordId'];
        }

        // Save the word for a user
        $query->insert('user_word');
        $query->setValue('word_id', $wordId);
        $query->setValue('user_id', $userId);
        $query->setValue('created_date', $query->expr()->literal(date('Y-m-d H:i:s')));
        $userWordId = $this->repo->create($query);

        $result = [];
        $result['userWordId'] = $userWordId;
        $result['wordId'] = $wordId;

        return $result;
    }

    /**
     * Persist a user
     * @param  Object $object user to persist
     * @return [type]         [description]
     */
    public function persist($object)
    {
        $errors = $this->validation($object);
        if (count($errors) > 0) {
            throw new BadRequestException(implode(', ', $errors));
        }

        $object->password = UserHelper::encryptPassword($object->password);

        return parent::persist($object);
    }
}
