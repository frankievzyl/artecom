<div id="makereview" class="modal fade inforpop" role="article">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="infoheader">Write Review</span>
                </div>
                <div class="modal-body">
                    <div id="reviewform" class="container-fluid">
                        <div class="rrating row">
                            <div class="col-xs-1 filler"></div>
                            <button class="col-xs-2 btn" type="button" id="rdecbtn" onclick="decRating()"><i class="fas fa-minus"></i></button>
                            <span class="rratingfld" style="display:none">0</span>
                            <div class="col-xs-6">
                                <div class="rratingbase">
                                <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                </div>
                                <div id="reditingstars">         
                                </div>
                            </div>
                            <button class="col-xs-2 btn" type="button" id="rincbtn" onclick="incRating()"><i class="fas fa-plus"></i></button>
                            <div class="col-xs-1 filler"></div>
                        </div>
                        <div id="rtext" class="row">
                            <div class="col-xs-1 filler"></div>
                            <textarea class="col-xs-10" id="rtextfld" placeholder="Enter your review here"></textarea>
                            <div class="col-xs-1 filler"></div>
                        </div>
                        <span class="row" id="rtextflderr"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="submitReview" >Post</button>
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>