<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/series", name="serie_")
 */
class SerieController extends AbstractController
{
    /**
     * @Route("", name="list")
     * @param SerieRepository $serieRepository
     * @return Response
     */
    public function list(SerieRepository $serieRepository): Response
    {
        //Méthode avec SQL (Source SerieRepository.php ligne 25)
        $series = $serieRepository->findBestSeries();

        //Méthode sans SQL
        //$series = $serieRepository->findBy([], ['popularity' => 'DESC', 'vote' => 'DESC'], 30);

        return $this->render('serie/list.html.twig', [
            "series" => $series
        ]);
    }

    /**
     * @Route("/details/{id}", name="details")
     * @param int $id
     * @param SerieRepository $serieRepository
     * @return Response
     */
    public function details(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        return $this->render('serie/details.html.twig', [
            "serie" => $serie
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $serie = new Serie();
        $serie->setDateCreated(new \DateTime());

        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);

        if($serieForm->isSubmitted() && $serieForm->isValid()){
            $entityManager->persist($serie);
            $entityManager->flush();

            $this->addFlash('success', 'Serie added ! Good job.');
            return $this->redirectToRoute('serie_details', ['id' => $serie->getId()]);
        }

        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }

    /**
     * @Route("/demo", name="em-demo")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function demo(EntityManagerInterface $entityManager): Response
    {
        // Façon alternative de récupérer l'entityManager :
        //$entityManager = $this->getDoctrine()->getManager();

        //créé une instance
        $serie = new Serie();

        //hydrate toutes les propriétés
        $serie->setName('Altered Carbon');
        $serie->setBackdrop('altered-carbon-68421.jpg');
        $serie->setPoster('altered-carbon-68421.jpg');
        $serie->setDateCreated(new \DateTime());
        $serie->setFirstAirDate(new \DateTime("- 1 year"));
        $serie->setLastAirDate(new \DateTime("- 6 month"));
        $serie->setGenres('Sci-Fi');
        $serie->setOverview('Altered Carbon is set in the 23rd 
        century when the human mind has been digitized and the soul it 
        self is transferable from one body to the next. Takeshi Kovacs, 
        a former elite interstellar warrior known as an Envoy who has been 
        imprisoned for 250 years, is downloaded into a future he\'d tried to stop.');
        $serie->setPopularity(123.00);
        $serie->setVote(8.2);
        $serie->setStatus('returning');
        $serie->setTmdbId(68421);

        dump($serie);

        //ENREGISTRER UNE SERIE
        $entityManager->persist($serie);
        $entityManager->flush();

        //MODIFIER UNE SERIE
        //$serie->setGenres('comedy');
        //$entityManager->flush();

        //SUPPRIMER UNE SERIE
        //$entityManager->remove($serie);
        //$entityManager->flush();

        return $this->render('serie/create.html.twig');
    }
}