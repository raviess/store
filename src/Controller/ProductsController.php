<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 */
class ProductsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('products', $this->paginate($this->Products));
        $this->set('_serialize', ['products']);
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Images']
        ]);
        $this->set('product', $product);
        $this->set('_serialize', ['product']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $product = $this->Products->newEntity();
        if ($this->request->is('post')) {
            $product = $this->Products->patchEntity($product, $this->request->data);
            
            //add image entry to database
            $images = TableRegistry::get('Images');
            $image = $images->newEntity();
            $data = $this->request->data['submittedImage'];
            $data1['image'] = $data['name'];            
            $data1['image_path'] = $this->wwwRoot;
            
            if ( $images->save($image)) {
            	$this->Flash->success(__('Image uploaded'));
            }
            else {
                $this->Flash->success(__('Image upload failed'));
            }
            
            //upload the file to the webroot folder
            
            
            //add product
            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The product could not be saved. Please, try again.'));
            }
        }
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $images = $this->Products->Images->find('list', ['limit' => 200]);
        $this->set(compact('product', 'categories', 'images'));
        $this->set('_serialize', ['product']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Images']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
                    
            //add image entry to database            
            $data = $this->request->data['submittedImage'];
            $data1 = [
            	'image_name' => $this->request->data['submittedImage']['name'],
            	'image_path' =>  WWW_ROOT.'images/'.$this->request->data['submittedImage']['name'],            
            ];
            
            $final_data = $this->request->data;
            $final_data['Images'] = $data1;
            unset($final_data['submittedImage']);

            
            $images = TableRegistry::get('Images');
            $image = $images->newEntity($data1);

            //save image entry in the table
            $image_id = $images->save($image);
            if ( $image_id ) {
                $msg = 'Image uploaded';
            }
            else {
                $msg = 'Image upload failed';
            }
            unset($final_data['Images']);
            $final_data['Images']['_ids'] = [$image_id->id];

            // pr($final_data);
            // die();

            $product = $this->Products->patchEntity($product, $final_data);
            $msg="";
            

            //add product image relationship
            //$product = $this->Products->get($id);
            //$image11 = $images->get($image_id);

            //$this->Products->Images->link($product, $image1);
            
            //move uploaded file 
            pr($data['tmp_name']);
            pr($data);
            pr( WWW_ROOT.'images/'.$data['name']);

            if(move_uploaded_file($data['tmp_name'],  WWW_ROOT.'images/'.$data['name'])) {
            	
            
            }        
            if ($this->Products->save($product)) {
            	$msg .= 'The product has been saved.'; 
                $this->Flash->success($msg);
                return $this->redirect(['action' => 'index']);
            } else {
            	$msg .= 'The product could not be saved. Please, try again.';
                $this->Flash->error($msg);
            }
        }
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $images = $this->Products->Images->find('list', ['limit' => 200]);
        $this->set(compact('product', 'categories', 'images'));
        $this->set('_serialize', ['product']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
