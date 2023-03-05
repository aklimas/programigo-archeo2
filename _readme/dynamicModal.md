#Modal
    <button 
    onClick="modal('detailsCalendar_0');" 
    type="button" class="btn btn-primary btn-sm">Test</button>

#Skrypt otwierający
    <script>
        function modal(theObject) {
            $.ajax({
                type: "POST",
                data: {'task': theObject},
                dataType: 'json',
                url: '{{ path('tickets_modal') }}',
                async: false, //you won't need that if nothing in your following code is dependend of the result,
                beforeSend: function (json) {
                    $(".ajaxModal").modal('show');
                },
            })
                .done(function (response) {

                    $('.ajaxModal .modal-title').html(response.title);
                    template = JSON.parse(response.template);
                    $('.ajaxModal .modal-body').html(template); //Change the html of the div with the id = "your_div"

                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    //  console.log(textStatus + jqXHR + errorThrown);
                });
        }
    </script>

#HTML MODAL
    <div class="modal ajaxModal fade" tabindex="-1"
         aria-labelledby="modalEdit" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-bs-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

#Kontroller
    #[Route('/modal', name: 'tickets_modal', methods: ['GET', 'POST'])]
    public function modal(Request $request): Response
    {
        // if ($request->isXmlHttpRequest()) {
        if ($request->request->get('task')) {
            $task = explode('_', $request->request->get('task'));
            switch ($task[0]) {
                case 'detailsCalendar' :
                    $json['title'] = 'Szczegóły dnia';
                    $_template = 'other/tickets/_content/_modalDetails.html.twig';
                    $data = ['id' => $task[1]];
                    break;
                default:
                    $_template = '';
                    $data = '';
            }

            $template = $this->render($_template, $data)->getContent();
            $json['template'] = json_encode($template);
            $response = new JsonResponse($json);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return new JsonResponse($request->request->all());
        // }else{
        //    return new JsonResponse();
        // }
    }