<?php
         
namespace App\Http\Controllers;
          
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use DataTables;
        
class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
   
        $products = Product::latest()->get();
        
        if ($request->ajax()) {
            $data = Product::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
   
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      
        return view('product',compact('products'));
    }
     
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product_details = Product::updateOrCreate(['id' => $request->product_id],
                ['product_name' => $request->product_name,'product_price' => $request->product_price, 'description' => $request->description]);
        $product_id = $product_details->id;

        if($request->hasFile('images')){
            $this->removeProductImages($product_id);

            $images = $request->file('images');
            $image_count = 0;
            foreach($images as $image){
                $extension = $image->getClientOriginalExtension();
                $image_name = time()."_".$image_count."_product.".$extension;
                $image->move('uploads/product_images/',$image_name);

                $product_image = new ProductImage();
                $product_image->product_id = $product_id;
                $product_image->image_name = $image_name;
                $product_image->save();
                $image_count++;
            }
        }
   
        return response()->json(['success'=>'Product saved successfully.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->removeProductImages($id);
        Product::find($id)->delete();
        
     
        return response()->json(['success'=>'Product deleted successfully.']);
    }
    private function removeProductImages($id){
        $product_images = ProductImage::where(["product_id"=>$id])->get();
        foreach($product_images as $product_image){
            unlink(public_path('uploads/product_images/'.$product_image->image_name));
        }
        ProductImage::where(["product_id"=>$id])->delete();
    }
}