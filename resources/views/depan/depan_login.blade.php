@include('components.header')

<div class="faq-area bg-gray default-padding">
    <!-- End Shape -->
    <div class="container">
        <div class="row">
            <div class="col-lg-2 mb-md-30 mb-xs-30">

            </div>

            <div class="col-lg-8 faq-style-one dark pl-50 pl-md-15 pl-xs-15">

                <!-- <h2 class="title mb-40">Form Login .</h2> -->

                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Login ETAM KERJA
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <!-- form -->
                                <div class="contact-form-style-one">
                                    <!-- <h5 class="sub-title">Have Questions?</h5>
                                    <h2 class="heading">Send us a Massage</h2> -->
                                    <form action="assets/mail/contact.php" method="POST"
                                        class="contact-form contact-form">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="username" name="username"
                                                        placeholder="Username" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="password" name="password"
                                                        placeholder="Password" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="username" name="username"
                                                        placeholder="XYTCDH" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="password" name="password"
                                                        placeholder="Masuukan Captca Disamping" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="submit" name="submit" id="submit">
                                                    <i class="fa fa-paper-plane"></i>Login
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Alert Message -->
                                        <div class="col-lg-12 alert-notification">
                                            <div id="message" class="alert-msg"></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- form -->
                            </div>
                        </div>
                    </div>


                </div>

            </div>

            <div class="col-lg-2 mb-md-30 mb-xs-30">

            </div>
        </div>
    </div>
</div>

@include('components.footer')
