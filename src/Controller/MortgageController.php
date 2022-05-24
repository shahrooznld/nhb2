<?php

namespace App\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

class MortgageController extends AbstractController
{

    private $xApiKey = '8974e55b-4b33-4dbf-8c6b-da4ebc20e466';
    /**
     * @Route("/mortgage/by_income", name="app_mortgage")
     * @Rest\QueryParam(
     *     name="nhg",
     *     requirements="(true|false)",
     *     default="false"
     * )
     * @Rest\QueryParam(
     *     name="old_student_loan_regulation",
     *     requirements="(true|false)",
     *     default="false"
     * )
     * @Rest\QueryParam(
     *     name="private_lease_amount",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     allowBlank=true,
     *     nullable=true
     * )
     * @Rest\QueryParam(
     *     name="private_lease_duration",
     *     requirements="\d+",
     *     allowBlank=true,
     *     nullable=true,
     *     default="0"
     * )
     *     * @Rest\QueryParam(
     *     name="private_lease_binding_offer_date",
     *     nullable=true
     * )
     * @Rest\QueryParam(
     *     name="duration",
     *     requirements="\d+",
     *     allowBlank=true,
     *     nullable=true,
     *     default="360"
     * )
     * @Rest\QueryParam(
     *     name="percentage",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     allowBlank=false,
     *     nullable=false
     * )
     * @Rest\QueryParam(
     *     name="rateFixation",
     *     requirements="\d+",
     *     allowBlank=true,
     *     nullable=true,
     *     default="10"
     * )
     * @Rest\QueryParam(
     *     name="notDeductible",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     allowBlank=true,
     *     nullable=true
     * )
     * @Rest\QueryParam(
     *     name="groundRent",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     allowBlank=true,
     *     nullable=true
     * )
     *  @Rest\QueryParam( name="first_person_income",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=false
     *     )
     *  @Rest\QueryParam( name="first_person_age",
     *     requirements="\d+",
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="first_person_dateOfBirth",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="first_person_alimony",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="first_person_loans",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="first_person_studentLoans",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="first_person_studentLoanStartDate",
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="second_person_income",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=false
     *     )
     *  @Rest\QueryParam( name="second_person_age",
     *     requirements="\d+",
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="second_person_dateOfBirth",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="second_person_alimony",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="second_person_loans",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="second_person_studentLoans",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     nullable=true,
     *     allowBlank=true
     *     )
     *  @Rest\QueryParam( name="second_person_studentLoanStartDate",
     *     nullable=true,
     *     allowBlank=true
     *     )
     */
    public function getMortgageByIncomeAction(ParamFetcherInterface $paramFetcher)
    {
        $url = 'https://api.hypotheekbond.nl/calculation/v1/mortgage/maximum-by-income';
        $params =$paramFetcher->all();

        foreach ($params as $key => $value) {
            if (strpos($key, 'first_person_') !== false) {
                $newKeyName = str_replace('first_person_','', $key);
                $params['person'][0][$newKeyName]= $value;
                unset($params[$key]);
                continue;
            }
            if (strpos($key, 'second_person_') !== false) {
                $newKeyName = str_replace('second_person_','', $key);
                $params['person'][1][$newKeyName]= $value;
                unset($params[$key]);
            }
        }
        $client = new Client();
        try {

            $response = $client->request('GET', $url, [
                'headers' => [
                    'x-api-key' => $this->xApiKey
                ],
                'query' => http_build_query($params)]);
            $responseBody = $response->getBody()->getContents();
            return new JsonResponse(json_decode($responseBody, true));
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * @Route("/mortgage/by_value", name="app_mortgage_by_value")
     * @Rest\QueryParam(
     *     name="objectvalue",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     strict=true,
     *     allowBlank=false,
     *     nullable=false
     * )
     * @Rest\QueryParam(
     *     name="duration",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     allowBlank=true,
     *     nullable=true,
     *     default="360"
     * )
     * @Rest\QueryParam(
     *     name="duration",
     *     requirements="[+-]?([0-9]*[.])?[0-9]+",
     *     allowBlank=true,
     *     nullable=true
     * )
     * @Rest\QueryParam(
     *     name="onlyUseIncludedLabels",
     *     requirements="(true|false)",
     *     default="false"
     * )
     */
    public function getMortgageByValueAction(ParamFetcherInterface $paramFetcher): JsonResponse
    {
        $url = 'https://api.hypotheekbond.nl/calculation/v1/mortgage/maximum-by-value';
        $params =$paramFetcher->all();
        $client = new Client();
        try {

            $response = $client->request('GET', $url, [
                'headers' => [
                    'x-api-key' => $this->xApiKey
                ],
                'query' => http_build_query($params)]);
            $responseBody = $response->getBody()->getContents();
            return new JsonResponse(json_decode($responseBody, true));
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }

    }
}
