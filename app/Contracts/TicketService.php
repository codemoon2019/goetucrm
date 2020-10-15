<?php
/**
 * Created by PhpStorm.
 * User: eunamagpantay
 * Date: 5/15/18
 * Time: 12:39 PM
 */

namespace App\Contracts;

use Illuminate\Http\Request;

interface TicketService
{

    /**
     * List all tickets
     * @return mixed
     */
    public function listTicket();

    /**
     * List all filter tickets
     *
     * @param null $type
     * @param null $product
     * @param null $department
     * @param null $users
     * @param null $partner
     * @param null $created
     * @param null $dueBy
     * @param null $status
     * @return mixed
     */
    public function listFilters($type = null, $product = null, $department = null, $users = null, $partner = null, $created = null, $dueBy = null, $status = null);

    /**
     * Product information
     *
     * @param $productId
     * @return mixed
     */
    public function listProductInformation($productId);

    /**
     * Update ticket
     *
     * @param Request $request
     * @param $id
     * @param bool $isComment
     * @return mixed
     */
    public function updateTicket(Request $request, $id, $isComment = false);

    /**
     * Update ticket status
     *
     * @param $ids
     * @param $status
     * @return mixed
     */
    public function updateStatus($ids, $status);

    /**
     * Show ticket header information
     *
     * @param $id
     * @return mixed
     */
    public function showInformation($id);

    /**
     * Adding ticket comments
     *
     * @param Request $request
     * @param $ticketId
     * @return mixed
     */
    public function addingComment(Request $request, $ticketId);

    /**
     * Get all companies
     *
     * @param $partnerTypeId
     * @return mixed
     */
    public function getCompanies($partnerTypeId,$partnerIds);

    /**
     * Assignees is comma delimited eg: 1,11,23
     *
     * @param $assignee
     * @return mixed
     */
    public function getTicketAssignees($assignee);

    /**
     * List all tickets for quick filter
     *
     * @param null $type
     * @param null $condition
     * @param string $value
     * @param string $owner
     * @param string $isSorting
     * @return mixed
     */
    public function listQuickFilter($type = null, $condition = null, $value = "", $owner = "", $isSorting = "");

}