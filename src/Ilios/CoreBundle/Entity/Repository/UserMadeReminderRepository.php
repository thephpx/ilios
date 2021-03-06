<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\UserMadeReminderDTO;

/**
 * Class UserMadeReminderRepository
 */
class UserMadeReminderRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:UserMadeReminder', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('IliosCoreBundle:UserMadeReminder', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var UserMadeReminderDTO[] $userMadeReminderDTOs */
        $userMadeReminderDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $userMadeReminderDTOs[$arr['id']] = new UserMadeReminderDTO(
                $arr['id'],
                $arr['note'],
                $arr['createdAt'],
                $arr['dueDate'],
                $arr['closed']
            );
        }
        $userMadeReminderIds = array_keys($userMadeReminderDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, user.id AS userId'
            )
            ->from('IliosCoreBundle:UserMadeReminder', 'x')
            ->join('x.user', 'user')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $userMadeReminderIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $userMadeReminderDTOs[$arr['xId']]->user = (int) $arr['userId'];
        }

        return array_values($userMadeReminderDTOs);
    }


    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.'.$sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}
